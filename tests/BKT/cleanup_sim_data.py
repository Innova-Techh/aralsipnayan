#!/usr/bin/env python3
"""
Clean up broken simulation data
"""

import mysql.connector

def cleanup_simulation_data():
    conn = mysql.connector.connect(
        host='localhost', 
        user='root', 
        password='', 
        database='aralsipnayandb'
    )
    cursor = conn.cursor()
    
    # Count existing simulated responses
    cursor.execute("SELECT COUNT(*) FROM question_responses WHERE response_id LIKE '%SIM%'")
    sim_count = cursor.fetchone()[0]
    print(f'Found {sim_count} simulated responses to remove')
    
    # Remove simulated data
    cursor.execute("DELETE FROM question_responses WHERE response_id LIKE '%SIM%'")
    cursor.execute("DELETE FROM assessments WHERE assessment_id LIKE '%SIM%'")
    cursor.execute("DELETE FROM users WHERE username LIKE 'test_student_%'")  # Remove test users by username
    cursor.execute("DELETE FROM questions WHERE question_id LIKE 'Q_%'")
    
    conn.commit()
    print('✓ Cleaned simulation data from database')
    
    # Verify cleanup
    cursor.execute('SELECT COUNT(*) FROM question_responses')
    total_responses = cursor.fetchone()[0]
    print(f'Remaining responses in database: {total_responses}')
    
    # Check what real data remains
    cursor.execute("SELECT user_id, COUNT(*) FROM question_responses GROUP BY user_id")
    remaining_data = cursor.fetchall()
    print(f'Remaining data by user: {remaining_data}')
    
    cursor.close()
    conn.close()

if __name__ == "__main__":
    cleanup_simulation_data()