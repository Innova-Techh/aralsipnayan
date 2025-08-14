import mysql.connector
from datetime import datetime
import json
import random
from typing import List, Dict, Optional, Tuple
from dataclasses import dataclass
from enum import Enum

# ================================
# ENUMS & DATA MODELS
# ================================

class Competency(Enum):
    NUMBER_ALGEBRA = "Number_Algebra"
    MEASUREMENT_GEOMETRY = "Measurement_Geometry"
    DATA_PROBABILITY = "Data_Probability"

class DifficultyLevel(Enum):
    BEGINNER = "Beginner"
    INTERMEDIATE = "Intermediate"
    ADVANCED = "Advanced"
    MIXED = "Mixed"  # for diagnostics only

class QuestionType(Enum):
    MULTIPLE_CHOICE = "Multiple_Choice"
    FILL_BLANKS = "Fill_Blanks"
    TRUE_FALSE = "True_False"
    DRAG_DROP = "Drag_Drop"
    CONNECT_DOTS = "Connect_Dots"

@dataclass
class Question:
    question_id: str
    competency: Competency
    difficulty_level: DifficultyLevel
    question_type: QuestionType
    question_text: str
    correct_answer: str
    choice_a: Optional[str] = None
    choice_b: Optional[str] = None
    choice_c: Optional[str] = None
    choice_d: Optional[str] = None
    hint_text: Optional[str] = None
    explanation: Optional[str] = None
    topic_tag: Optional[str] = None
    points: Optional[int] = None

@dataclass
class StudentMastery:
    user_id: int
    competency: Competency
    current_difficulty_level: DifficultyLevel
    mastery_probability: float
    total_questions_answered: int
    correct_answers: int
    p_learn: float = 0.1
    p_guess: float = 0.25
    p_slip: float = 0.1
    diagnostic_completed: bool = False

# ================================
# DATABASE
# ================================

class DatabaseManager:
    def __init__(self, host='localhost', user='root', password='', database='aralsipnayan'):
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None

    def connect(self):
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database,
                autocommit=True
            )
            print("✅ Database connected")
            return True
        except mysql.connector.Error as err:
            print(f"❌ DB connect failed: {err}")
            return False

    def disconnect(self):
        if self.connection and self.connection.is_connected():
            self.connection.close()
            print("📡 Database disconnected")

    def execute_query(self, query: str, params: tuple = None):
        try:
            cursor = self.connection.cursor(dictionary=True)
            cursor.execute(query, params)
            if query.strip().upper().startswith('SELECT'):
                rows = cursor.fetchall()
                cursor.close()
                return rows
            else:
                self.connection.commit()
                cursor.close()
                return True
        except mysql.connector.Error as err:
            print(f"❌ Query failed: {err}")
            print(f"   SQL: {query}")
            print(f"   Params: {params}")
            return None

    def execute_many(self, query: str, params_list: List[tuple]):
        try:
            cursor = self.connection.cursor()
            cursor.executemany(query, params_list)
            self.connection.commit()
            cursor.close()
            return True
        except mysql.connector.Error as err:
            print(f"❌ Batch failed: {err}")
            return False

# ================================
# BKT ENGINE
# ================================

class BayesianKnowledgeTracing:
    def __init__(self, db: DatabaseManager):
        self.db = db

    def initialize_student_mastery(self, user_id: int) -> bool:
        """Ensure mastery rows exist for all competencies (diagnostic not completed)."""
        for comp in Competency:
            self.db.execute_query("""
                INSERT IGNORE INTO student_competency_mastery
                (user_id, competency, current_difficulty_level, mastery_probability,
                 total_questions_answered, correct_answers, p_learn, p_guess, p_slip,
                 diagnostic_completed)
                VALUES (%s, %s, 'Beginner', 0.300, 0, 0, 0.100, 0.250, 0.100, FALSE)
            """, (user_id, comp.value))
        return True

    def get_student_mastery(self, user_id: int, competency: Competency) -> Optional[StudentMastery]:
        row = self.db.execute_query("""
            SELECT * FROM student_competency_mastery
            WHERE user_id = %s AND competency = %s
        """, (user_id, competency.value))
        if not row:
            self.initialize_student_mastery(user_id)
            row = self.db.execute_query("""
                SELECT * FROM student_competency_mastery
                WHERE user_id = %s AND competency = %s
            """, (user_id, competency.value))
        if not row:
            return None
        d = row[0]
        return StudentMastery(
            user_id=d['user_id'],
            competency=Competency(d['competency']),
            current_difficulty_level=DifficultyLevel(d['current_difficulty_level']),
            mastery_probability=float(d['mastery_probability']),
            total_questions_answered=d['total_questions_answered'],
            correct_answers=d['correct_answers'],
            p_learn=float(d['p_learn']),
            p_guess=float(d['p_guess']),
            p_slip=float(d['p_slip']),
            diagnostic_completed=bool(d['diagnostic_completed'])
        )

    def update_mastery_realtime(self, user_id: int, competency: Competency, is_correct: bool) -> float:
        m = self.get_student_mastery(user_id, competency)
        if not m:
            return 0.3

        pK = m.mastery_probability
        if is_correct:
            p_correct = pK * (1 - m.p_slip) + (1 - pK) * m.p_guess
            new_pK = (pK * (1 - m.p_slip)) / p_correct if p_correct > 0 else pK
        else:
            p_incorrect = pK * m.p_slip + (1 - pK) * (1 - m.p_guess)
            new_pK = (pK * m.p_slip) / p_incorrect if p_incorrect > 0 else pK

        final = new_pK + (1 - new_pK) * m.p_learn
        final = max(0.0, min(1.0, final))

        self.db.execute_query("""
            UPDATE student_competency_mastery
            SET mastery_probability = %s,
                total_questions_answered = total_questions_answered + 1,
                correct_answers = correct_answers + %s,
                last_updated = NOW()
            WHERE user_id = %s AND competency = %s
        """, (final, 1 if is_correct else 0, user_id, competency.value))

        return final

# ================================
# QUESTION SELECTION (single-competency)
# ================================

class QuestionSelector:
    def __init__(self, db: DatabaseManager):
        self.db = db

    def _get_cooldown_blocklist(self, user_id: int, competency: Competency) -> List[str]:
        rows = self.db.execute_query("""
            SELECT question_id
            FROM question_cooldowns
            WHERE user_id = %s AND competency = %s
              AND is_active = TRUE AND cooldown_until > NOW()
        """, (user_id, competency.value)) or []
        return [r['question_id'] for r in rows]

    def _fetch_pool(self, competency: Competency, difficulties: List[DifficultyLevel],
                    exclude_ids: List[str]) -> List[Dict]:
        placeholders = ','.join(['%s'] * len(difficulties))
        sql = f"""
            SELECT question_id, competency, difficulty_level, question_type, question_text,
                   correct_answer, choice_a, choice_b, choice_c, choice_d,
                   hint_text, explanation, topic_tag,
                   COALESCE(points, 1) AS points
            FROM questions
            WHERE competency = %s
              AND difficulty_level IN ({placeholders})
              AND is_active = TRUE
        """
        params = [competency.value] + [d.value for d in difficulties]
        if exclude_ids:
            not_in = ','.join(['%s'] * len(exclude_ids))
            sql += f" AND question_id NOT IN ({not_in})"
            params += exclude_ids
        # no ORDER BY RAND() — we’ll Fisher–Yates later
        rows = self.db.execute_query(sql, tuple(params)) or []
        return rows

    def _ensure_topic_diversity(self, pool: List[Dict], target_n: int) -> List[Dict]:
        """
        Greedy round-robin by topic_tag to ensure diversity.
        Falls back to fill from remaining if topics are scarce.
        """
        by_topic: Dict[str, List[Dict]] = {}
        for q in pool:
            topic = q.get('topic_tag') or "_untagged"
            by_topic.setdefault(topic, []).append(q)

        # Shuffle lists to avoid bias
        for lst in by_topic.values():
            random.shuffle(lst)

        selected = []
        # Round-robin pick
        topics = list(by_topic.keys())
        t_idx = 0
        while len(selected) < target_n and any(by_topic.values()):
            topic = topics[t_idx % len(topics)]
            if by_topic[topic]:
                selected.append(by_topic[topic].pop())
            t_idx += 1
            # break if all lists empty
            if all(len(v) == 0 for v in by_topic.values()):
                break

        # If still short, fill from remaining pool (whatever left)
        if len(selected) < target_n:
            remaining = []
            for v in by_topic.values():
                remaining.extend(v)
            random.shuffle(remaining)
            for q in remaining:
                if len(selected) >= target_n:
                    break
                selected.append(q)

        return selected[:target_n]

    @staticmethod
    def fisher_yates_shuffle(items: List[Dict]) -> List[Dict]:
        arr = items[:]
        n = len(arr)
        for i in range(n - 1, 0, -1):
            j = random.randint(0, i)
            arr[i], arr[j] = arr[j], arr[i]
        return arr

    def build_assessment_questions(
        self,
        user_id: int,
        competency: Competency,
        difficulty_band: DifficultyLevel,
        total_questions: int,
        exclude_cooldowns: bool = True,
        diagnostic: bool = False
    ) -> List[Question]:
        """
        Returns a Fisher–Yates shuffled list of Question objects for ONE competency.
        - diagnostic=True => mixed difficulty (B, I, A)
        - otherwise only difficulty_band
        """
        exclude_ids = self._get_cooldown_blocklist(user_id, competency) if exclude_cooldowns else []

        if diagnostic:
            difficulties = [DifficultyLevel.BEGINNER, DifficultyLevel.INTERMEDIATE, DifficultyLevel.ADVANCED]
        else:
            difficulties = [difficulty_band]

        pool_rows = self._fetch_pool(competency, difficulties, exclude_ids)

        if not pool_rows:
            return []

        # Topic diversity then Fisher–Yates
        diversified = self._ensure_topic_diversity(pool_rows, total_questions)
        shuffled = self.fisher_yates_shuffle(diversified)

        return [
            Question(
                question_id=r['question_id'],
                competency=Competency(r['competency']),
                difficulty_level=DifficultyLevel(r['difficulty_level']),
                question_type=QuestionType(r['question_type']),
                question_text=r['question_text'],
                correct_answer=r['correct_answer'],
                choice_a=r.get('choice_a'),
                choice_b=r.get('choice_b'),
                choice_c=r.get('choice_c'),
                choice_d=r.get('choice_d'),
                hint_text=r.get('hint_text'),
                explanation=r.get('explanation'),
                topic_tag=r.get('topic_tag'),
                points=r.get('points', 1)
            )
            for r in shuffled
        ]

# ================================
# SESSION MANAGER (Single-Competency)
# ================================

class LearningSessionManager:
    def __init__(self, db: DatabaseManager):
        self.db = db
        self.bkt = BayesianKnowledgeTracing(db)
        self.selector = QuestionSelector(db)

    @staticmethod
    def _band_from_mastery(p: float) -> DifficultyLevel:
        if p < 0.4:
            return DifficultyLevel.BEGINNER
        if p <= 0.8:
            return DifficultyLevel.INTERMEDIATE
        return DifficultyLevel.ADVANCED

    @staticmethod
    def _next_level(level: DifficultyLevel) -> DifficultyLevel:
        return {
            DifficultyLevel.BEGINNER: DifficultyLevel.INTERMEDIATE,
            DifficultyLevel.INTERMEDIATE: DifficultyLevel.ADVANCED,
            DifficultyLevel.ADVANCED: DifficultyLevel.ADVANCED
        }[level]

    @staticmethod
    def _prev_level(level: DifficultyLevel) -> DifficultyLevel:
        return {
            DifficultyLevel.ADVANCED: DifficultyLevel.INTERMEDIATE,
            DifficultyLevel.INTERMEDIATE: DifficultyLevel.BEGINNER,
            DifficultyLevel.BEGINNER: DifficultyLevel.BEGINNER
        }[level]

    def start_competency_session(
        self,
        user_id: int,
        competency: Competency,
        session_type: str = "Adaptive",
        total_questions: int = 15
    ) -> Tuple[str, List[Question], float, DifficultyLevel, bool]:
        """
        Phase 1 & 2:
        - If first time (diagnostic not completed for this competency): run 15Q diagnostic (mixed).
        - Else: use mastery -> difficulty band; build 15Q within this competency & that difficulty.
        Returns (session_id, questions, initial_mastery, difficulty_level_used, diagnostic_flag)
        """
        self.bkt.initialize_student_mastery(user_id)
        mastery = self.bkt.get_student_mastery(user_id, competency)
        if not mastery:
            raise RuntimeError("Mastery row missing.")

        diagnostic_needed = not mastery.diagnostic_completed and session_type in ("Adaptive", "Diagnostic")
        if diagnostic_needed:
            difficulty_level = DifficultyLevel.MIXED
            questions = self.selector.build_assessment_questions(
                user_id, competency, DifficultyLevel.BEGINNER, total_questions, exclude_cooldowns=False, diagnostic=True
            )
            use_session_type = "Diagnostic"
        else:
            difficulty_level = self._band_from_mastery(mastery.mastery_probability)
            questions = self.selector.build_assessment_questions(
                user_id, competency, difficulty_level, total_questions, exclude_cooldowns=True, diagnostic=False
            )
            use_session_type = "Adaptive"

        if len(questions) < total_questions:
            print(f"⚠️ Not enough questions available for {competency.value} ({len(questions)}/{total_questions}).")

        session_id = f"STU{user_id:03d}-{competency.value.split('_')[0]}-{datetime.now().strftime('%Y%m%d')}-{random.randint(100, 999):03d}"
        q_ids = [q.question_id for q in questions]

        self.db.execute_query("""
            INSERT INTO assessment_sessions
            (session_id, user_id, assessment_id, session_type, competency, difficulty_level,
             questions_json, total_questions, start_time, status,
             initial_mastery_probability)
            VALUES (%s, %s, NULL, %s, %s, %s, %s, %s, NOW(), 'in_progress', %s)
        """, (session_id, user_id, use_session_type, competency.value,
              difficulty_level.value, json.dumps(q_ids), len(q_ids),
              round(mastery.mastery_probability, 3)))

        print(f"🎯 Started {use_session_type} session {session_id} for {competency.value} with {len(q_ids)} questions.")
        return session_id, questions, mastery.mastery_probability, difficulty_level, diagnostic_needed

    def record_answer(
        self,
        user_id: int,
        session_id: str,
        question: Question,
        user_answer: str,
        is_correct: bool,
        time_taken: Optional[int] = None,
        hint_used: bool = False
    ) -> bool:
        """Phase 3: store response, update BKT, apply cooldown."""
        comp = question.competency
        # points policy
        points = question.points or 1
        if hint_used and is_correct:
            points = max(1, points // 2)
        elif not is_correct:
            points = 0

        mastery_before = self.bkt.get_student_mastery(user_id, comp)
        m_before = mastery_before.mastery_probability if mastery_before else 0.3
        m_after = self.bkt.update_mastery_realtime(user_id, comp, is_correct)

        order_row = self.db.execute_query("""
            SELECT COALESCE(MAX(question_order), 0) + 1 AS next_order
            FROM question_responses
            WHERE session_id = %s
        """, (session_id,))
        q_order = (order_row[0]['next_order'] if order_row else 1)

        ok = self.db.execute_query("""
            INSERT INTO question_responses
            (session_id, question_id, question_order, student_answer, is_correct,
             points_earned, response_time_seconds, mastery_before, mastery_after,
             hint_used, hint_penalty_applied)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, (session_id, question.question_id, q_order, user_answer, is_correct,
              points, time_taken, round(m_before, 3), round(m_after, 3),
              hint_used, bool(hint_used and is_correct)))
        if not ok:
            return False

        # set cooldown
        self._set_question_cooldown(user_id, question.question_id, comp, is_correct, session_id)
        return True

    def _set_question_cooldown(self, user_id: int, question_id: str,
                               competency: Competency, is_correct: bool, session_id: str):
        self.db.execute_query("""
            UPDATE question_cooldowns
            SET is_active = FALSE
            WHERE user_id = %s AND question_id = %s
        """, (user_id, question_id))

        cooldown_hours = 24 if is_correct else 4
        reason = 'correct_answer' if is_correct else 'wrong_answer'
        self.db.execute_query("""
            INSERT INTO question_cooldowns
            (user_id, question_id, competency, cooldown_until, cooldown_reason,
             cooldown_hours, session_id, is_active)
            VALUES (%s, %s, %s, DATE_ADD(NOW(), INTERVAL %s HOUR), %s, %s, %s, TRUE)
        """, (user_id, question_id, competency.value, cooldown_hours, reason, cooldown_hours, session_id))

    def end_session_and_postprocess(
        self,
        user_id: int,
        session_id: str,
        competency: Competency,
        initial_mastery: float
    ) -> Dict:
        """Phase 4: finalize stats, apply delta rules, set diagnostic flag if needed."""
        stats = self.db.execute_query("""
            SELECT 
                COUNT(*) AS total_questions,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) AS correct_answers,
                SUM(points_earned) AS total_points,
                AVG(CASE WHEN is_correct = 1 THEN 1.0 ELSE 0.0 END) AS accuracy_rate
            FROM question_responses
            WHERE session_id = %s
        """, (session_id,))

        total_q = stats[0]['total_questions'] if stats else 0
        correct = stats[0]['correct_answers'] or 0
        total_points = stats[0]['total_points'] or 0
        acc = float(stats[0]['accuracy_rate'] or 0.0)

        # current mastery after last question
        mastery_now = self.bkt.get_student_mastery(user_id, competency)
        final_mastery = mastery_now.mastery_probability if mastery_now else initial_mastery
        delta = final_mastery - initial_mastery

        # Determine action
        action = "MAINTAIN"
        new_level = mastery_now.current_difficulty_level
        if delta >= 0.15:
            action = "PROMOTE"
            new_level = self._next_level(new_level)
        elif delta <= -0.10:
            action = "REMEDIATE"
            new_level = self._prev_level(new_level)
        else:
            action = "MAINTAIN"

        # Update mastery row level if changed
        self.db.execute_query("""
            UPDATE student_competency_mastery
            SET current_difficulty_level = %s,
                last_assessment_date = NOW()
            WHERE user_id = %s AND competency = %s
        """, (new_level.value, user_id, competency.value))

        # If this was a diagnostic session, mark completed for this competency
        sess = self.db.execute_query("""
            SELECT session_type FROM assessment_sessions WHERE session_id = %s
        """, (session_id,))
        if sess and sess[0]['session_type'] == 'Diagnostic':
            self.db.execute_query("""
                UPDATE student_competency_mastery
                SET diagnostic_completed = TRUE, diagnostic_date = NOW()
                WHERE user_id = %s AND competency = %s
            """, (user_id, competency.value))

        # Close session
        self.db.execute_query("""
            UPDATE assessment_sessions
            SET end_time = NOW(),
                status = 'completed',
                questions_answered = %s,
                correct_answers = %s,
                total_points_earned = %s,
                accuracy_rate = %s,
                final_mastery_probability = %s
            WHERE session_id = %s
        """, (total_q, correct, total_points, round(acc, 3), round(final_mastery, 3), session_id))

        return {
            "total_questions": total_q,
            "correct_answers": correct,
            "accuracy_percentage": round(acc * 100, 1),
            "initial_mastery": round(initial_mastery, 3),
            "final_mastery": round(final_mastery, 3),
            "delta": round(delta, 3),
            "action": action,
            "new_level": new_level.value
        }

# ================================
# DIAGNOSTIC (Per-Competency wrapper)
# ================================

class DiagnosticManager:
    def __init__(self, db: DatabaseManager):
        self.db = db
        self.sessions = LearningSessionManager(db)

    def run_if_needed(self, user_id: int, competency: Competency) -> Optional[Dict]:
        """
        If competency has no diagnostic yet, run a 15Q diagnostic.
        Returns session summary dict if run, else None.
        """
        mastery = BayesianKnowledgeTracing(self.db).get_student_mastery(user_id, competency)
        if mastery and mastery.diagnostic_completed:
            return None

        session_id, questions, initial_mastery, _, _ = self.sessions.start_competency_session(
            user_id, competency, session_type="Diagnostic", total_questions=15
        )
        # NOTE: In production, questions are answered by the student on UI.
        # This function only orchestrates the session; do NOT auto-answer here.
        # Return session_id so the app can proceed to render and collect answers.
        return {
            "session_id": session_id,
            "question_ids": [q.question_id for q in questions],
            "initial_mastery": round(initial_mastery, 3)
        }

# ================================
# ANALYTICS
# ================================

class LearningAnalytics:
    def __init__(self, db: DatabaseManager):
        self.db = db

    def get_student_progress_report(self, user_id: int) -> Dict:
        report = {
            "user_id": user_id,
            "competencies": {},
            "recent_performance": {},
            "recommendations": []
        }
        # competency overview
        rows = self.db.execute_query("""
            SELECT competency, current_difficulty_level, mastery_probability,
                   total_questions_answered, correct_answers, accuracy_rate,
                   diagnostic_completed
            FROM student_competency_mastery
            WHERE user_id = %s
        """, (user_id,)) or []
        for r in rows:
            report["competencies"][r['competency']] = {
                "current_level": r['current_difficulty_level'],
                "mastery_probability": float(r['mastery_probability']),
                "questions_answered": int(r['total_questions_answered']),
                "correct_answers": int(r['correct_answers']),
                "accuracy_rate_pct": round(float(r['accuracy_rate'] or 0) * 100, 1),
                "diagnostic_completed": bool(r['diagnostic_completed'])
            }

        # last 7 days performance
        recent = self.db.execute_query("""
            SELECT DATE(qr.answered_at) AS answer_date,
                   COUNT(*) AS questions_answered,
                   SUM(CASE WHEN qr.is_correct = 1 THEN 1 ELSE 0 END) AS correct_answers
            FROM question_responses qr
            JOIN assessment_sessions s ON qr.session_id = s.session_id
            WHERE s.user_id = %s
              AND qr.answered_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(qr.answered_at)
            ORDER BY answer_date DESC
        """, (user_id,)) or []
        for d in recent:
            date_str = d['answer_date'].strftime('%Y-%m-%d')
            qa = int(d['questions_answered'])
            ca = int(d['correct_answers'] or 0)
            acc = (ca / qa) * 100 if qa else 0.0
            report["recent_performance"][date_str] = {"questions": qa, "accuracy_pct": round(acc, 1)}

        # simple recommendations
        recs = []
        for comp, data in report["competencies"].items():
            p = data["mastery_probability"]
            lvl = data["current_level"]
            if p < 0.4:
                recs.append(f"{comp}: Focus Beginner drills; prioritize weak topics.")
            elif p <= 0.8:
                recs.append(f"{comp}: Keep practicing Intermediate; mix in spaced review.")
            else:
                recs.append(f"{comp}: Attempt Advanced assessments; maintain via spaced practice.")
        report["recommendations"] = recs
        return report

# ================================
# UTILITY: RUN A FULL ASSESSMENT FLOW (for integration)
# ================================

def run_competency_assessment_flow(
    db: DatabaseManager,
    user_id: int,
    competency: Competency
) -> Dict:
    """
    Orchestrates the **exact** thesis-compliant flow for ONE competency:
      Phase 1: initial calibration (diagnostic if first time) / difficulty from mastery thresholds
      Phase 2: build 15Q assessment (cooldowns, topic diversity, Fisher–Yates)
      Phase 3: real-time BKT update per answer (call record_answer from UI controller)
      Phase 4: post-processing on completion (delta rules -> maintain/promote/remediate)
    Returns a dict with session_id and the queued questions for the UI to present.
    """
    lm = LearningSessionManager(db)
    session_id, questions, initial_mastery, diff_level, diagnostic_flag = lm.start_competency_session(
        user_id=user_id,
        competency=competency,
        session_type="Adaptive",
        total_questions=15
    )
    return {
        "session_id": session_id,
        "competency": competency.value,
        "diagnostic": diagnostic_flag,
        "difficulty_level_used": diff_level.value,
        "initial_mastery": round(initial_mastery, 3),
        "questions": [q.__dict__ for q in questions]  # you’ll serialize to the client
    }

# ================================
# NOTES FOR INTEGRATION (important)
# ================================
# 1) Present questions in the exact order returned by run_competency_assessment_flow.
# 2) After each student answer on the UI, call:
#       lm.record_answer(user_id, session_id, question, user_answer, is_correct, time_taken, hint_used)
#    where `question` is the Question object you already queued for that position.
# 3) After all 15 questions are answered, call:
#       lm.end_session_and_postprocess(user_id, session_id, competency, initial_mastery)
#    and show the result (action, new_level, delta, breakdown).
# 4) Cooldowns are automatically applied per question in THIS competency only.
# 5) Topic diversity: greedy round-robin over topic_tag.
# 6) Fisher–Yates is used for final ordering, not MySQL RAND().
