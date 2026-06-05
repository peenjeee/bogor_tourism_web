"""
Text Preprocessing Module for Indonesian Tourism Text
Uses Sastrawi for stemming and stopword removal
"""

import re
import nltk
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory

# Download NLTK data
nltk.download('punkt', quiet=True)
nltk.download('stopwords', quiet=True)

# Initialize Sastrawi
stemmer_factory = StemmerFactory()
try:
    stemmer = stemmer_factory.create_stemmer()
except AttributeError:
    stemmer = stemmer_factory.createStemmer()

stopword_factory = StopWordRemoverFactory()
try:
    indonesian_stopwords = set(stopword_factory.get_stop_words())
except AttributeError:
    indonesian_stopwords = set(stopword_factory.getStopWords())

# Custom stopwords
custom_stopwords = {
    'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'dengan', 'ini', 'itu', 'atau',
    'pada', 'adalah', 'juga', 'akan', 'bisa', 'ada', 'serta', 'tersebut', 'dapat',
    'http', 'https', 'www', 'com', 'id'
}
all_stopwords = indonesian_stopwords.union(custom_stopwords)


def preprocess_text(text):
    """
    Preprocess Indonesian text for NLP tasks.
    
    Args:
        text: Raw text string
        
    Returns:
        Cleaned and processed text
    """
    if not text or text == '' or str(text).lower() == 'nan':
        return ''
    
    text = str(text).lower()
    
    # Remove URLs
    text = re.sub(r'http\S+|www\S+|\S+@\S+', '', text)
    
    # Remove numbers
    text = re.sub(r'\d+', '', text)
    
    # Remove special characters
    text = re.sub(r'[^\w\s]', ' ', text)
    
    # Remove extra whitespace
    text = re.sub(r'\s+', ' ', text).strip()
    
    # Tokenize
    try:
        from nltk.tokenize import word_tokenize
        tokens = word_tokenize(text)
    except:
        tokens = text.split()
    
    # Remove stopwords and short tokens, apply stemming
    tokens = [stemmer.stem(t) for t in tokens if t not in all_stopwords and len(t) > 2]
    
    return ' '.join(tokens)


def extract_ngrams(text, n):
    """
    Extract n-grams from text.
    
    Args:
        text: Preprocessed text string
        n: n-gram size (1=unigram, 2=bigram, 3=trigram)
        
    Returns:
        List of n-grams
    """
    if not text:
        return []
    
    tokens = text.split()
    
    if len(tokens) < n:
        return tokens if n == 1 else []
    
    return [' '.join(tokens[i:i+n]) for i in range(len(tokens)-n+1)]
