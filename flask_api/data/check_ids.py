import pandas as pd
import os

try:
    df = pd.read_csv('data_with_keywords.csv')
    print("CSV Headers:", df.columns.tolist())
    
    if 'id' in df.columns:
        print("\nFirst 5 rows (ID vs ID from Index):")
        # standard index is 0..N
        for idx, row in df.head(5).iterrows():
            print(f"Index: {idx} | ID Column: {row['id']} | Name: {row['nama']}")
            
        print("\nLast 5 rows:")
        for idx, row in df.tail(5).iterrows():
            print(f"Index: {idx} | ID Column: {row['id']} | Name: {row['nama']}")
            
        # Check alignment
        mismatch = df[df.index != df['id']]
        if not mismatch.empty:
            print(f"\nMismatch found! {len(mismatch)} rows have Index != ID")
            print("Example Mismatch:")
            print(mismatch[['id', 'nama']].head(3))
        else:
            print("\nIndex matches ID exactly!")
            
    else:
        print("\n'id' column NOT FOUND in CSV. Pure index usage.")
        
except Exception as e:
    print(f"Error: {e}")
