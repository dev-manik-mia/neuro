# Cortex: Laravel AI Framework

A unified AI interface for Laravel — LLMs, embeddings, vector databases, RAG pipelines, agents, and more.

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x%20|%2012.x%20|%2013.x-red)](https://laravel.com)
[![Packagist](https://img.shields.io/badge/packagist-manik/cortex-green)](https://packagist.org/packages/manik/cortex)

---

- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [LLM Providers](#llm-providers)
- [Embedding Providers](#embedding-providers)
- [Vector Databases](#vector-databases)
- [RAG Pipeline](#rag-pipeline)
- [Agents & Tool Calling](#agents--tool-calling)
- [Memory](#memory)
- [Image Generation](#image-generation)
- [Speech Processing](#speech-processing)
- [Document Ingestion & Chunking](#document-ingestion--chunking)
- [Testing](#testing)
- [Events & Observability](#events--observability)
- [Architecture](#architecture)

---

## Installation

```bash
composer require manik/cortex
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=cortex-config
```

Run migrations (for persistent memory and document storage):

```bash
php artisan migrate
```

## Configuration

Add the following to your `.env` file based on the providers you use:

### LLM Providers

```env
# OpenAI
AI_DEFAULT_LLM=openai
OPENAI_API_KEY=sk-...
OPENAI_LLM_MODEL=gpt-4o

# Anthropic
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_LLM_MODEL=claude-3-5-sonnet-20241022

# Google Gemini
GEMINI_API_KEY=...
GEMINI_LLM_MODEL=gemini-2.0-flash

# Local Ollama
AI_DEFAULT_LLM=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_LLM_MODEL=llama3

# xAI Grok
XAI_API_KEY=...
XAI_LLM_MODEL=grok-2

# Mistral
MISTRAL_API_KEY=...
MISTRAL_LLM_MODEL=mistral-large-latest

# Cohere
COHERE_API_KEY=...
COHERE_LLM_MODEL=command-r-plus
```

### Embedding Providers

```env
# Default
AI_DEFAULT_EMBEDDING=openai
OPENAI_API_KEY=sk-...
OPENAI_EMBEDDING_MODEL=text-embedding-3-small

# Local Ollama (no API key needed)
AI_DEFAULT_EMBEDDING=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_EMBEDDING_MODEL=llama3

# Gemini
GEMINI_API_KEY=...
GEMINI_EMBEDDING_MODEL=text-embedding-004

# Mistral
MISTRAL_API_KEY=...
MISTRAL_EMBEDDING_MODEL=mistral-embed

# Cohere
COHERE_API_KEY=...
COHERE_EMBEDDING_MODEL=embed-english-v3.0
```

### Vector Databases

```env
# Default
AI_DEFAULT_VECTOR=qdrant
QDRANT_HOST=http://localhost:6333

# Pinecone
AI_DEFAULT_VECTOR=pinecone
PINECONE_API_KEY=...
PINECONE_ENVIRONMENT=...
PINECONE_INDEX_HOST=https://...pinecone.io

# pgvector
AI_DEFAULT_VECTOR=pgvector
PGVECTOR_CONNECTION=pgsql
PGVECTOR_TABLE=vector_embeddings
PGVECTOR_DIMENSIONS=1536

# Weaviate
AI_DEFAULT_VECTOR=weaviate
WEAVIATE_HOST=http://localhost:8080

# Milvus
AI_DEFAULT_VECTOR=milvus
MILVUS_HOST=http://localhost:19530

# Chroma
AI_DEFAULT_VECTOR=chroma
CHROMA_HOST=http://localhost:8000
```

### RAG Configuration

```env
AI_RAG_CHUNK_STRATEGY=recursive
AI_RAG_CHUNK_SIZE=1000
AI_RAG_CHUNK_OVERLAP=200
AI_RAG_TOP_K=5
AI_RAG_MIN_SCORE=0.0
```

### Cache & Rate Limiting

```env
AI_CACHE_ENABLED=false
AI_CACHE_STORE=redis
AI_CACHE_TTL=3600

AI_RATE_LIMIT_ENABLED=false
AI_RATE_LIMIT_MAX=60
AI_RATE_LIMIT_DECAY=60
```

## Quick Start

```php
use Illuminate\Support\Facades\Cortex;
```

### Chat Completion

```php
$response = Cortex::chat()
    ->provider('openai')
    ->model('gpt-4o')
    ->message('Explain Laravel to a beginner')
    ->chat();

// $response['content'] => string
// $response['role'] => 'assistant'
```

### Streaming Chat

```php
$stream = Cortex::chat()
    ->provider('openai')
    ->message('Write a poem about Laravel')
    ->stream();

foreach ($stream as $chunk) {
    echo $chunk['content'];
    ob_flush();
    flush();
}
```

### Embeddings

```php
$result = Cortex::chat()
    ->text('The text to embed')
    ->embed('openai');

// $result['embedding'] => array of floats
// $result['dimensions'] => int
```

Batch embed:

```php
$results = Cortex::chat()
    ->embedBatch(['text one', 'text two', 'text three'], 'openai');
```

### Vector Search

```php
$results = Cortex::vector()
    ->driver('qdrant')
    ->search('my_collection', $vector, ['top_k' => 10]);
```

## LLM Providers

Use the `Cortex::llm()` method to access the LLM manager directly:

```php
$driver = Cortex::llm()->driver('openai');
$response = $driver->chat([['role' => 'user', 'content' => 'Hello!']]);
```

### Supported Providers

| Provider     | Driver Key   | Chat | Stream | Tools | Config Key         |
|-------------|--------------|------|--------|-------|---------------------|
| OpenAI       | `openai`     | ✅   | ✅     | ✅    | `ai.llm.openai`     |
| Anthropic    | `anthropic`  | ✅   | ✅     | ✅    | `ai.llm.anthropic`  |
| Google Gemini| `gemini`     | ✅   | ✅     | ❌    | `ai.llm.gemini`     |
| Ollama       | `ollama`     | ✅   | ✅     | ✅    | `ai.llm.ollama`     |
| xAI Grok     | `grok`       | ✅   | ✅     | ✅    | `ai.llm.grok`       |
| Mistral      | `mistral`    | ✅   | ✅     | ✅    | `ai.llm.mistral`    |
| Cohere       | `cohere`     | ✅   | ✅     | ✅    | `ai.llm.cohere`     |

### Tool Calling

```php
$response = Cortex::chat()
    ->provider('openai')
    ->model('gpt-4o')
    ->message('What is the weather in Paris?')
    ->tools([
        [
            'type' => 'function',
            'function' => [
                'name' => 'get_weather',
                'description' => 'Get the weather for a city',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'city' => ['type' => 'string'],
                    ],
                ],
            ],
        ],
    ]);
```

### Custom Ollama Setup

Ollama runs locally with no API key required:

```bash
# Install Ollama
brew install ollama

# Pull a model
ollama pull llama3

# Run the server
ollama serve
```

```env
AI_DEFAULT_LLM=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_LLM_MODEL=llama3
```

```php
$response = Cortex::chat()
    ->provider('ollama')
    ->model('llama3')
    ->message('Hello, how are you?')
    ->chat();
```

## Embedding Providers

| Provider   | Driver Key   | Dimensions | Config Key              |
|-----------|--------------|------------|--------------------------|
| OpenAI     | `openai`     | 1536       | `ai.embedding.openai`    |
| Ollama     | `ollama`     | 4096       | `ai.embedding.ollama`    |
| Gemini     | `gemini`     | 768        | `ai.embedding.gemini`    |
| Mistral    | `mistral`    | 1024       | `ai.embedding.mistral`   |
| Cohere     | `cohere`     | 1024       | `ai.embedding.cohere`    |

```php
$vector = Cortex::embedding()
    ->driver('openai')
    ->embed('Your text here');
```

## Vector Databases

### Supported Databases

| Database | Driver Key  | Create | Upsert | Search | Delete | Filter | Config Key          |
|----------|-------------|--------|--------|--------|--------|--------|----------------------|
| Qdrant   | `qdrant`    | ✅     | ✅     | ✅     | ✅     | ✅     | `ai.vector.qdrant`   |
| Pinecone | `pinecone`  | ❌     | ✅     | ✅     | ✅     | ✅     | `ai.vector.pinecone` |
| pgvector | `pgvector`  | ✅     | ✅     | ✅     | ✅     | ✅     | `ai.vector.pgvector` |
| Weaviate | `weaviate`  | ✅     | ✅     | ✅     | ✅     | ✅     | `ai.vector.weaviate` |
| Milvus   | `milvus`    | ✅     | ✅     | ✅     | ✅     | ✅     | `ai.vector.milvus`   |
| Chroma   | `chroma`    | ✅     | ✅     | ✅     | ✅     | ✅     | `ai.vector.chroma`   |

### Basic Usage

```php
// Get a vector driver
$vector = Cortex::vector()->driver('qdrant');

// Create a collection
$vector->createCollection('knowledge', 1536);

// Upsert vectors
$vector->upsert('knowledge', [
    [
        'id' => '1',
        'vector' => [0.1, 0.2, ...],
        'payload' => ['text' => 'Some content', 'source' => 'docs'],
    ],
]);

// Search
$results = $vector->search('knowledge', [0.1, 0.2, ...], [
    'top_k' => 10,
    'filter' => ['source' => 'docs'],
]);

// Delete
$vector->delete('knowledge', '1');
```

### pgvector Setup

```bash
# Add pgvector extension to your PostgreSQL database
CREATE EXTENSION vector;

# Then use in your code
Cortex::vector()->driver('pgvector')
    ->createCollection('embeddings', 1536);

Cortex::vector()->driver('pgvector')
    ->upsert('embeddings', [
        [
            'id' => 'doc_1',
            'vector' => [0.1, 0.2, ...],
            'payload' => ['text' => 'Hello world'],
        ],
    ]);
```

### Qdrant Setup

```bash
# With Docker
docker run -p 6333:6333 qdrant/qdrant

# Then use in your code
Cortex::vector()->driver('qdrant')
    ->createCollection('documents', 1536);
```

## RAG Pipeline

The RAG (Retrieval Augmented Generation) pipeline retrieves relevant context from a vector store and uses it to answer questions.

### Basic RAG

```php
$response = Cortex::rag()
    ->collection('knowledge_base')
    ->question('What is Laravel?')
    ->answer();

// $response['answer'] => string (the LLM's answer with context)
// $response['sources'] => array (the retrieved chunks)
// $response['tokens'] => array (token usage)
```

### Using the Pipeline Directly

```php
$pipeline = Cortex::rag()->pipeline();

$response = $pipeline
    ->collection('knowledge_base')
    ->question('What is Laravel?')
    ->topK(10)
    ->minScore(0.5)
    ->answer();

// Just search without LLM
$results = $pipeline->search();
```

### The RAG Flow

```
User Question
    ↓
[1] Embed the question
    ↓
[2] Search vector database for similar content
    ↓
[3] Build context from retrieved chunks
    ↓
[4] Send question + context to LLM
    ↓
Answer with sources
```

## Agents & Tool Calling

### Creating an Agent

```php
$agent = Cortex::agent('openai')
    ->session('user-123')
    ->maxSteps(5)
    ->tool('get_time', function () {
        return now()->toDateTimeString();
    }, 'Get the current date and time')
    ->tool('calculate', function (float $a, string $op, float $b) {
        return match ($op) { '+' => $a + $b, '-' => $a - $b, '*' => $a * $b, '/' => $a / $b };
    }, 'Perform a calculation');

$result = $agent->run('What time is it?');
// $result['response'] => string
// $result['steps'] => int
```

### Registering Tools via Manager

```php
// Manager level
use Illuminate\Support\Facades\Cortex;

$driver = Cortex::llm()->driver('openai');
$driver->tools($messages, [
    [
        'type' => 'function',
        'function' => [
            'name' => 'search_web',
            'description' => 'Search the web for information',
            'parameters' => [
                'type' => 'object',
                'required' => ['query'],
                'properties' => [
                    'query' => ['type' => 'string', 'description' => 'Search query'],
                ],
            ],
        ],
    ],
]);
```

## Memory

### Available Drivers

| Driver         | Key            | Storage   | Description                     |
|---------------|----------------|-----------|---------------------------------|
| Session       | `session`      | Laravel session | Per-request/session memory      |
| Conversation  | `conversation` | In-memory | Runtime conversation history    |
| Persistent    | `persistent`   | Database  | Long-term persistent storage    |

### Usage

```php
// Using the memory manager
Cortex::memory()->driver('session')->add('session-1', [
    'role' => 'user',
    'content' => 'Hello!',
]);

$history = Cortex::memory()->driver('session')->get('session-1');
// Returns array of messages, limited by config

Cortex::memory()->driver('session')->clear('session-1');
```

### Persistent Memory

```php
// Requires running the migration
Cortex::memory()->driver('persistent')->add('user-456', [
    'role' => 'user',
    'content' => 'Remember my name is John',
]);

$history = Cortex::memory()->driver('persistent')->get('user-456');
```

## Image Generation

### OpenAI DALL-E

```php
$result = Cortex::image()
    ->driver('openai')
    ->generate('A serene mountain landscape at sunset', [
        'size' => '1024x1024',
        'quality' => 'hd',
    ]);

// $result['url'] => string
// $result['revised_prompt'] => string
```

### Edit Image

```php
$result = Cortex::image()
    ->driver('openai')
    ->edit('/path/to/image.png', 'Add a rainbow to the sky');
```

### Variations

```php
$result = Cortex::image()
    ->driver('openai')
    ->variations('/path/to/image.png', ['n' => 3]);
```

## Speech Processing

### Text-to-Speech

```php
$audioContent = Cortex::speech()
    ->driver('openai')
    ->synthesize('Hello, welcome to Laravel AI!', [
        'voice' => 'alloy',
        'model' => 'tts-1',
    ]);

// Save to file
Storage::put('audio/welcome.mp3', $audioContent);
```

### Speech-to-Text

```php
$transcription = Cortex::speech()
    ->driver('openai')
    ->transcribe('/path/to/audio.mp3');

// $transcription['text'] => string
```

## Document Ingestion & Chunking

### Supported Formats

| Format    | Auto-detected |
|-----------|--------------|
| `.txt`    | ✅           |
| `.md`     | ✅           |
| `.html`   | ✅           |
| `.csv`    | ✅           |
| `.json`   | ✅           |

### Ingest a Document

```php
Cortex::rag()->ingestion()
    ->ingestFromPath(storage_path('docs/laravel-intro.md'), 'knowledge_base');
```

### Ingest Raw Content

```php
Cortex::rag()->ingestion()
    ->ingestRaw('# Laravel\nLaravel is a PHP framework...', 'knowledge_base', [
        'source' => 'manual',
        'author' => 'John',
    ]);
```

### Chunking Strategies

| Strategy            | Class                        | Description                              |
|--------------------|------------------------------|------------------------------------------|
| Fixed Size         | `FixedSizeChunking`          | Split by character count with overlap    |
| Recursive          | `RecursiveChunking`          | Split by paragraphs → sentences → chars |
| Semantic           | `SemanticChunking`           | Split by headings and blank lines        |
| Sliding Window     | `SlidingWindowChunking`      | Overlapping windows with stride          |

```php
use Manik\Cortex\RAG\Chunking\SemanticChunking;

Cortex::rag()->ingestion()
    ->setChunkStrategy(new SemanticChunking)
    ->ingestRaw($markdownContent, 'docs');
```

### Full Ingestion Pipeline

```
Document
    ↓ Chunking (FixedSize / Recursive / Semantic / SlidingWindow)
Chunks
    ↓ Embedding (via configured embedding provider)
Vector Embeddings
    ↓ Upsert to Vector Store
Stored in Collection
```

## Testing

### Fake Responses

```php
use Illuminate\Support\Facades\Cortex;

// Enable fake mode
Cortex::fake();

// All chat calls now return fake responses
$response = Cortex::chat()
    ->message('This will not hit the API')
    ->chat();

// $response['content'] === 'fake response'
```

### Fake Embeddings

```php
Cortex::fake();

$result = Cortex::chat()
    ->text('Test text')
    ->embed('openai');

// Returns zeroed-out embedding vector with 1536 dimensions
```

## Events & Observability

### Events

| Event              | Description                    | Payload                                 |
|--------------------|--------------------------------|-----------------------------------------|
| `MessageSending`   | Before an LLM call is made     | provider, model, messages, options      |
| `MessageReceived`  | After an LLM response          | provider, model, response, latency      |
| `EmbeddingCreated` | After embedding is generated   | provider, model, text, dimensions       |
| `VectorStored`     | After vectors are upserted     | provider, collection, record_count      |
| `DocumentIndexed`  | After a document is indexed    | collection, document, chunk_count       |

```php
use Manik\Cortex\Events\MessageReceived;

Event::listen(MessageReceived::class, function (MessageReceived $event) {
    Log::info('LLM call completed', [
        'provider' => $event->provider,
        'model' => $event->model,
        'latency' => $event->latency,
    ]);
});
```

### Observability Configuration

```php
// config/cortex.php
'observability' => [
    'track_cost' => env('AI_TRACK_COST', false),
    'track_tokens' => env('AI_TRACK_TOKENS', false),
    'track_latency' => env('AI_TRACK_LATENCY', false),
    'store' => env('AI_OBSERVABILITY_STORE', 'log'),
],
```

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Facade (Cortex::)                        │
├─────────────────────────────────────────────────────────┤
│                    AIClient                             │
├─────────────────────────────────────────────────────────┤
│                   CortexManager                       │
├──────┬──────┬──────┬──────┬──────┬──────┬──────┬───────┤
│  LLM │Embed │Vector│ Image│Speech│ RAG  │Memory│ Agent │
│Manager│Mgr   │Mgr   │ Mgr  │ Mgr  │ Mgr  │ Mgr  │       │
├──────┼──────┼──────┼──────┼──────┼──────┼──────┼───────┤
│Driver│Driver│Driver│Driver│Driver│Pipeline│Drivers│Agent│
│  AI  │  AI  │  DB  │  AI  │  AI  │+Chunk │Session│+Tools│
│Anthrop│Ollama│Qdrant│DALL-E│TTS   │+Ingest│Persist│      │
│Gemini│Mistral│Pinecn│      │STT   │+Rerank│       │      │
│Ollama│Cohere│pgvec │      │      │       │       │      │
│Others│      │Weav. │      │      │       │       │      │
└──────┴──────┴──────┴──────┴──────┴──────┴──────┴───────┘
```

### Extending with Custom Drivers

You can register custom drivers at runtime:

```php
// Custom LLM driver
Cortex::llm()->extend('my-provider', function ($app) {
    return new MyCustomDriver(config('ai.llm.my-provider'));
});

// Custom embedding driver
Cortex::embedding()->extend('my-embedder', function ($app) {
    return new MyEmbedder(config('ai.embedding.my-embedder'));
});

// Custom vector driver
Cortex::vector()->extend('my-vector-db', function ($app) {
    return new MyVectorDB(config('ai.vector.my-vector-db'));
});
```

Then add the corresponding config to `config/cortex.php` and use it:

```php
$response = Cortex::chat()
    ->provider('my-provider')
    ->message('Hello')
    ->chat();
```

---

## License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.
