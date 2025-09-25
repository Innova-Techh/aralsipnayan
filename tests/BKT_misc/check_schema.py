#!/usr/bin/env python3
"""
Check Database Schema
"""

import mysql.connector

try:
    conn = mysql.connector.connect(
        host='localhost',
        user='root',
        password='',
        database='aralsipnayandb'
    )
    cursor = conn.cursor()
    
    cursor.execute("DESCRIBE question_responses")
    columns = cursor.fetchall()
    
    print("question_responses table columns:")
    for col in columns:
        print(f"  {col[0]} - {col[1]}")
    
    cursor.execute("SELECT COUNT(*) FROM question_responses")
    count = cursor.fetchone()[0]
    print(f"\nTotal responses: {count}")
    
    # Show sample data
    cursor.execute("SELECT * FROM question_responses LIMIT 5")
    samples = cursor.fetchall()
    print(f"\nSample data:")
    for i, sample in enumerate(samples):
        print(f"  Row {i+1}: {sample}")
    
    conn.close()
    
except Exception as e:
    print(f"Error: {e}")