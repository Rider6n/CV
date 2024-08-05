import streamlit as st
from PyPDF2 import PdfReader
from bs4 import BeautifulSoup
from langchain.text_splitter import RecursiveCharacterTextSplitter
from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.vectorstores import FAISS
from langchain.chains.question_answering import load_qa_chain
from langchain.chat_models import ChatOpenAI

# Load the API key from Streamlit secrets
OPENAI_API_KEY = st.secrets["openai"]["api_key"]

# Function to read PDF files
def read_pdf(file):
    pdf_reader = PdfReader(file)
    text = ""
    for page in pdf_reader.pages:
        text += page.extract_text()
    return text

# Function to read HTML files
def read_html(file):
    html_content = file.read().decode('utf-8')
    soup = BeautifulSoup(html_content, 'html.parser')
    return soup.get_text()

# Streamlit app
st.header("My first Chatbot")

with st.sidebar:
    st.title("Your Documents")
    file = st.file_uploader("Upload a PDF or HTML file and start asking questions", type=["pdf", "html"])

# Extract the text
if file is not None:
    file_type = file.name.split('.')[-1]
    if file_type == 'pdf':
        text = read_pdf(file)
    elif file_type == 'html':
        text = read_html(file)

    # Break it into chunks
    text_splitter = RecursiveCharacterTextSplitter(
        separators=["\n"],
        chunk_size=1000,
        chunk_overlap=150,
        length_function=len
    )
    chunks = text_splitter.split_text(text)

    # Generating embeddings
    embeddings = OpenAIEmbeddings(openai_api_key=OPENAI_API_KEY)

    # Creating vector store - FAISS
    vector_store = FAISS.from_texts(chunks, embeddings)

    # Get user question
    user_question = st.text_input("Type your question here")

    # Do similarity search
    if user_question:
        match = vector_store.similarity_search(user_question)

        # Define the LLM
        llm = ChatOpenAI(
            openai_api_key=OPENAI_API_KEY,
            temperature=0,
            max_tokens=1000,
            model_name="gpt-3.5-turbo"
        )

        # Output results
        # chain -> take the question, get relevant document, pass it to the LLM, generate the output
        chain = load_qa_chain(llm, chain_type="stuff")
        response = chain.run(input_documents=match, question=user_question)
        st.write(response)

