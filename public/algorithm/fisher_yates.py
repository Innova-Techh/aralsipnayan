#!/usr/bin/env python3
"""
Fisher-Yates Shuffle Algorithm Implementation

This module provides an implementation of the Fisher-Yates shuffle algorithm
for randomizing question order in educational assessments. The Fisher-Yates
shuffle ensures uniform distribution and unbiased randomization compared to
simple random selection methods.

Author: AralSipnayan BKT System
Date: August 15, 2025
"""

import random
from typing import List, Any, Optional


class FisherYatesShuffle:
    """
    Implementation of the Fisher-Yates shuffle algorithm for unbiased randomization.
    
    The Fisher-Yates shuffle (also known as the Knuth shuffle) is an algorithm 
    for generating a random permutation of a finite sequence. It ensures that
    every permutation is equally likely, providing true randomization.
    """
    
    def __init__(self, seed: Optional[int] = None):
        """
        Initialize the Fisher-Yates shuffle with optional random seed.
        
        Args:
            seed (Optional[int]): Random seed for reproducible results.
                                 If None, uses system time for randomization.
        """
        if seed is not None:
            random.seed(seed)
    
    def shuffle(self, items: List[Any]) -> List[Any]:
        """
        Perform Fisher-Yates shuffle on a list of items.
        
        The algorithm works by:
        1. Starting from the last element
        2. Picking a random index from 0 to current position
        3. Swapping the current element with the randomly selected element
        4. Moving to the previous element and repeating
        
        Args:
            items (List[Any]): List of items to shuffle
            
        Returns:
            List[Any]: A new list with items shuffled using Fisher-Yates algorithm
            
        Example:
            >>> shuffler = FisherYatesShuffle(seed=42)
            >>> original = [1, 2, 3, 4, 5]
            >>> shuffled = shuffler.shuffle(original)
            >>> print(shuffled)  # [3, 1, 5, 2, 4] (example output)
        """
        if not items:
            return []
        
        # Create a copy to avoid modifying the original list
        shuffled_items = items.copy()
        n = len(shuffled_items)
        
        # Fisher-Yates shuffle algorithm
        for i in range(n - 1, 0, -1):
            # Pick a random index from 0 to i (inclusive)
            j = random.randint(0, i)
            
            # Swap elements at positions i and j
            shuffled_items[i], shuffled_items[j] = shuffled_items[j], shuffled_items[i]
        
        return shuffled_items
    
    def shuffle_in_place(self, items: List[Any]) -> None:
        """
        Perform Fisher-Yates shuffle in-place (modifies the original list).
        
        This version modifies the original list instead of creating a new one,
        which can be more memory efficient for large lists.
        
        Args:
            items (List[Any]): List of items to shuffle in-place
            
        Example:
            >>> shuffler = FisherYatesShuffle()
            >>> my_list = [1, 2, 3, 4, 5]
            >>> shuffler.shuffle_in_place(my_list)
            >>> print(my_list)  # [3, 1, 5, 2, 4] (example output, original list modified)
        """
        n = len(items)
        
        for i in range(n - 1, 0, -1):
            j = random.randint(0, i)
            items[i], items[j] = items[j], items[i]
    
    def shuffle_with_constraints(self, items: List[Any], 
                               constraint_func: callable = None) -> List[Any]:
        """
        Perform Fisher-Yates shuffle with optional constraints.
        
        This method allows for constrained shuffling where certain conditions
        must be met. Useful for educational assessments where you want randomization
        but with some rules (e.g., no two questions of the same topic adjacent).
        
        Args:
            items (List[Any]): List of items to shuffle
            constraint_func (callable): Function that takes the current shuffled list
                                       and returns True if constraints are satisfied
                                       
        Returns:
            List[Any]: Shuffled list that satisfies the constraints
            
        Note:
            This method has a maximum of 1000 attempts to find a valid shuffle.
            If no valid shuffle is found, it returns the best attempt.
        """
        if not items or not constraint_func:
            return self.shuffle(items)
        
        max_attempts = 1000
        best_shuffle = self.shuffle(items)
        
        for attempt in range(max_attempts):
            shuffled = self.shuffle(items)
            
            if constraint_func(shuffled):
                return shuffled
            
            # Keep track of the best attempt (in case we can't satisfy constraints)
            best_shuffle = shuffled
        
        # Return best attempt if constraints couldn't be satisfied
        return best_shuffle
    
    def partial_shuffle(self, items: List[Any], k: int) -> List[Any]:
        """
        Perform partial Fisher-Yates shuffle (only shuffle first k elements).
        
        This is useful when you only need a subset of shuffled items,
        which is more efficient than shuffling the entire list.
        
        Args:
            items (List[Any]): List of items to partially shuffle
            k (int): Number of elements to shuffle from the beginning
            
        Returns:
            List[Any]: List with first k elements shuffled
            
        Example:
            >>> shuffler = FisherYatesShuffle()
            >>> items = list(range(1, 11))  # [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
            >>> result = shuffler.partial_shuffle(items, 5)
            >>> # First 5 elements are shuffled, rest remain in original order
        """
        if not items or k <= 0:
            return items.copy()
        
        if k > len(items):
            k = len(items)
        
        shuffled_items = items.copy()
        n = len(shuffled_items)
        
        # Only shuffle the first k elements
        for i in range(min(k, n)):
            # Pick a random index from i to n-1
            j = random.randint(i, n - 1)
            shuffled_items[i], shuffled_items[j] = shuffled_items[j], shuffled_items[i]
        
        return shuffled_items


class AssessmentShuffle:
    """
    Specialized shuffle class for educational assessments.
    
    This class extends Fisher-Yates shuffle with specific methods
    for educational content, including topic diversity and difficulty balancing.
    """
    
    def __init__(self, seed: Optional[int] = None):
        """Initialize assessment shuffle with Fisher-Yates shuffler."""
        self.shuffler = FisherYatesShuffle(seed)
    
    def shuffle_questions(self, questions: List[dict]) -> List[dict]:
        """
        Shuffle questions using Fisher-Yates algorithm.
        
        Args:
            questions (List[dict]): List of question dictionaries
            
        Returns:
            List[dict]: Shuffled list of questions
        """
        return self.shuffler.shuffle(questions)
    
    def shuffle_with_topic_diversity(self, questions: List[dict], 
                                   topic_key: str = 'topic_tag') -> List[dict]:
        """
        Shuffle questions while ensuring topic diversity.
        
        This method tries to avoid having consecutive questions from the same topic.
        
        Args:
            questions (List[dict]): List of question dictionaries
            topic_key (str): Key in question dict that contains topic information
            
        Returns:
            List[dict]: Shuffled list with improved topic diversity
        """
        def topic_diversity_constraint(shuffled_questions):
            """Check if consecutive questions have different topics."""
            if len(shuffled_questions) < 2:
                return True
            
            consecutive_same_topic = 0
            max_consecutive = 2  # Allow at most 2 consecutive questions from same topic
            
            for i in range(1, len(shuffled_questions)):
                current_topic = shuffled_questions[i].get(topic_key, '')
                previous_topic = shuffled_questions[i-1].get(topic_key, '')
                
                if current_topic == previous_topic and current_topic != '':
                    consecutive_same_topic += 1
                    if consecutive_same_topic >= max_consecutive:
                        return False
                else:
                    consecutive_same_topic = 0
            
            return True
        
        return self.shuffler.shuffle_with_constraints(questions, topic_diversity_constraint)
    
    def shuffle_with_difficulty_balance(self, questions: List[dict],
                                      difficulty_key: str = 'difficulty_level') -> List[dict]:
        """
        Shuffle questions while balancing difficulty progression.
        
        This tries to avoid having all hard questions at the end or beginning.
        
        Args:
            questions (List[dict]): List of question dictionaries
            difficulty_key (str): Key in question dict that contains difficulty level
            
        Returns:
            List[dict]: Shuffled list with balanced difficulty progression
        """
        def difficulty_balance_constraint(shuffled_questions):
            """Check if difficulty is reasonably distributed."""
            if len(shuffled_questions) < 4:
                return True
            
            # Check first quarter and last quarter for difficulty balance
            quarter = len(shuffled_questions) // 4
            
            first_quarter = shuffled_questions[:quarter]
            last_quarter = shuffled_questions[-quarter:]
            
            # Count advanced questions in first and last quarters
            def count_advanced(q_list):
                return sum(1 for q in q_list 
                          if q.get(difficulty_key, '').lower() == 'advanced')
            
            first_advanced = count_advanced(first_quarter)
            last_advanced = count_advanced(last_quarter)
            
            # Avoid having too many advanced questions concentrated in one quarter
            max_advanced_per_quarter = max(1, quarter // 2)
            
            return (first_advanced <= max_advanced_per_quarter and 
                   last_advanced <= max_advanced_per_quarter)
        
        return self.shuffler.shuffle_with_constraints(questions, difficulty_balance_constraint)


# Utility functions for easy import
def fisher_yates_shuffle(items: List[Any], seed: Optional[int] = None) -> List[Any]:
    """
    Convenience function for basic Fisher-Yates shuffle.
    
    Args:
        items (List[Any]): Items to shuffle
        seed (Optional[int]): Random seed for reproducible results
        
    Returns:
        List[Any]: Shuffled items
    """
    shuffler = FisherYatesShuffle(seed)
    return shuffler.shuffle(items)


def shuffle_assessment_questions(questions: List[dict], 
                               ensure_diversity: bool = True,
                               seed: Optional[int] = None) -> List[dict]:
    """
    Convenience function for shuffling assessment questions with diversity.
    
    Args:
        questions (List[dict]): Questions to shuffle
        ensure_diversity (bool): Whether to ensure topic diversity
        seed (Optional[int]): Random seed for reproducible results
        
    Returns:
        List[dict]: Shuffled questions
    """
    assessment_shuffle = AssessmentShuffle(seed)
    
    if ensure_diversity:
        return assessment_shuffle.shuffle_with_topic_diversity(questions)
    else:
        return assessment_shuffle.shuffle_questions(questions)


if __name__ == "__main__":
    # Example usage and testing
    print("Fisher-Yates Shuffle Algorithm Implementation")
    print("=" * 50)
    
    # Test basic shuffle
    test_items = list(range(1, 11))
    shuffler = FisherYatesShuffle(seed=42)  # Use seed for reproducible results
    
    print(f"Original: {test_items}")
    shuffled = shuffler.shuffle(test_items)
    print(f"Shuffled: {shuffled}")
    
    # Test with questions
    sample_questions = [
        {"id": "Q1", "topic_tag": "Algebra", "difficulty_level": "Beginner"},
        {"id": "Q2", "topic_tag": "Geometry", "difficulty_level": "Intermediate"},
        {"id": "Q3", "topic_tag": "Algebra", "difficulty_level": "Advanced"},
        {"id": "Q4", "topic_tag": "Statistics", "difficulty_level": "Beginner"},
        {"id": "Q5", "topic_tag": "Geometry", "difficulty_level": "Intermediate"},
    ]
    
    print(f"\nOriginal questions: {[q['id'] for q in sample_questions]}")
    
    assessment_shuffle = AssessmentShuffle(seed=123)
    shuffled_questions = assessment_shuffle.shuffle_with_topic_diversity(sample_questions)
    
    print(f"Shuffled questions: {[q['id'] for q in shuffled_questions]}")
    print(f"Topics order: {[q['topic_tag'] for q in shuffled_questions]}")
