<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Providers
    |--------------------------------------------------------------------------
    |
    | Here you may specify which providers should be used by default for
    | each AI capability. You can override these at runtime using the
    | ->provider() fluent method.
    |
    */
    'defaults' => [
        'llm' => env('AI_DEFAULT_LLM', 'openai'),
        'embedding' => env('AI_DEFAULT_EMBEDDING', 'openai'),
        'vector' => env('AI_DEFAULT_VECTOR', 'qdrant'),
        'image' => env('AI_DEFAULT_IMAGE', 'openai'),
        'speech' => env('AI_DEFAULT_SPEECH', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LLM Providers
    |--------------------------------------------------------------------------
    */
    'llm' => [

        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_LLM_MODEL', 'gpt-4o'),
            'temperature' => (float) env('OPENAI_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 4096),
            'timeout' => (int) env('OPENAI_TIMEOUT', 60),
        ],

        'anthropic' => [
            'driver' => 'anthropic',
            'api_key' => env('ANTHROPIC_API_KEY'),
            'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
            'model' => env('ANTHROPIC_LLM_MODEL', 'claude-3-5-sonnet-20241022'),
            'temperature' => (float) env('ANTHROPIC_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 4096),
            'timeout' => (int) env('ANTHROPIC_TIMEOUT', 60),
        ],

        'gemini' => [
            'driver' => 'gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'model' => env('GEMINI_LLM_MODEL', 'gemini-2.0-flash'),
            'temperature' => (float) env('GEMINI_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 4096),
            'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        ],

        'ollama' => [
            'driver' => 'ollama',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_LLM_MODEL', 'llama3'),
            'temperature' => (float) env('OLLAMA_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('OLLAMA_MAX_TOKENS', 4096),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
        ],

        'grok' => [
            'driver' => 'grok',
            'api_key' => env('XAI_API_KEY'),
            'base_url' => env('XAI_BASE_URL', 'https://api.x.ai/v1'),
            'model' => env('XAI_LLM_MODEL', 'grok-2'),
            'temperature' => (float) env('XAI_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('XAI_MAX_TOKENS', 4096),
            'timeout' => (int) env('XAI_TIMEOUT', 60),
        ],

        'mistral' => [
            'driver' => 'mistral',
            'api_key' => env('MISTRAL_API_KEY'),
            'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
            'model' => env('MISTRAL_LLM_MODEL', 'mistral-large-latest'),
            'temperature' => (float) env('MISTRAL_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('MISTRAL_MAX_TOKENS', 4096),
            'timeout' => (int) env('MISTRAL_TIMEOUT', 60),
        ],

        'cohere' => [
            'driver' => 'cohere',
            'api_key' => env('COHERE_API_KEY'),
            'base_url' => env('COHERE_BASE_URL', 'https://api.cohere.ai/v1'),
            'model' => env('COHERE_LLM_MODEL', 'command-r-plus'),
            'temperature' => (float) env('COHERE_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('COHERE_MAX_TOKENS', 4096),
            'timeout' => (int) env('COHERE_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Embedding Providers
    |--------------------------------------------------------------------------
    */
    'embedding' => [

        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
            'dimensions' => (int) env('OPENAI_EMBEDDING_DIMENSIONS', 1536),
            'timeout' => (int) env('OPENAI_TIMEOUT', 60),
        ],

        'ollama' => [
            'driver' => 'ollama',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_EMBEDDING_MODEL', 'llama3'),
            'dimensions' => (int) env('OLLAMA_EMBEDDING_DIMENSIONS', 4096),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
        ],

        'gemini' => [
            'driver' => 'gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'model' => env('GEMINI_EMBEDDING_MODEL', 'text-embedding-004'),
            'dimensions' => (int) env('GEMINI_EMBEDDING_DIMENSIONS', 768),
            'timeout' => (int) env('GEMINI_TIMEOUT', 60),
        ],

        'mistral' => [
            'driver' => 'mistral',
            'api_key' => env('MISTRAL_API_KEY'),
            'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
            'model' => env('MISTRAL_EMBEDDING_MODEL', 'mistral-embed'),
            'dimensions' => (int) env('MISTRAL_EMBEDDING_DIMENSIONS', 1024),
            'timeout' => (int) env('MISTRAL_TIMEOUT', 60),
        ],

        'cohere' => [
            'driver' => 'cohere',
            'api_key' => env('COHERE_API_KEY'),
            'base_url' => env('COHERE_BASE_URL', 'https://api.cohere.ai/v1'),
            'model' => env('COHERE_EMBEDDING_MODEL', 'embed-english-v3.0'),
            'dimensions' => (int) env('COHERE_EMBEDDING_DIMENSIONS', 1024),
            'timeout' => (int) env('COHERE_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vector Database Providers
    |--------------------------------------------------------------------------
    */
    'vector' => [

        'qdrant' => [
            'driver' => 'qdrant',
            'host' => env('QDRANT_HOST', 'http://localhost:6333'),
            'api_key' => env('QDRANT_API_KEY'),
            'timeout' => (int) env('QDRANT_TIMEOUT', 30),
        ],

        'pinecone' => [
            'driver' => 'pinecone',
            'api_key' => env('PINECONE_API_KEY'),
            'environment' => env('PINECONE_ENVIRONMENT'),
            'index_host' => env('PINECONE_INDEX_HOST'),
            'timeout' => (int) env('PINECONE_TIMEOUT', 30),
        ],

        'pgvector' => [
            'driver' => 'pgvector',
            'connection' => env('PGVECTOR_CONNECTION', 'pgsql'),
            'table' => env('PGVECTOR_TABLE', 'vector_embeddings'),
            'dimensions' => (int) env('PGVECTOR_DIMENSIONS', 1536),
            'timeout' => (int) env('PGVECTOR_TIMEOUT', 30),
        ],

        'weaviate' => [
            'driver' => 'weaviate',
            'host' => env('WEAVIATE_HOST', 'http://localhost:8080'),
            'api_key' => env('WEAVIATE_API_KEY'),
            'timeout' => (int) env('WEAVIATE_TIMEOUT', 30),
        ],

        'milvus' => [
            'driver' => 'milvus',
            'host' => env('MILVUS_HOST', 'http://localhost:19530'),
            'username' => env('MILVUS_USERNAME'),
            'password' => env('MILVUS_PASSWORD'),
            'timeout' => (int) env('MILVUS_TIMEOUT', 30),
        ],

        'chroma' => [
            'driver' => 'chroma',
            'host' => env('CHROMA_HOST', 'http://localhost:8000'),
            'tenant' => env('CHROMA_TENANT', 'default'),
            'database' => env('CHROMA_DATABASE', 'default'),
            'timeout' => (int) env('CHROMA_TIMEOUT', 30),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Generation
    |--------------------------------------------------------------------------
    */
    'image' => [
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_IMAGE_MODEL', 'dall-e-3'),
            'size' => env('OPENAI_IMAGE_SIZE', '1024x1024'),
            'quality' => env('OPENAI_IMAGE_QUALITY', 'standard'),
            'timeout' => (int) env('OPENAI_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Speech Processing
    |--------------------------------------------------------------------------
    */
    'speech' => [
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_SPEECH_MODEL', 'tts-1'),
            'voice' => env('OPENAI_SPEECH_VOICE', 'alloy'),
            'timeout' => (int) env('OPENAI_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Configuration
    |--------------------------------------------------------------------------
    */
    'memory' => [
        'default' => env('AI_DEFAULT_MEMORY', 'session'),

        'drivers' => [
            'session' => [
                'driver' => 'session',
                'limit' => (int) env('AI_MEMORY_LIMIT', 20),
            ],
            'conversation' => [
                'driver' => 'conversation',
                'limit' => (int) env('AI_MEMORY_LIMIT', 50),
            ],
            'persistent' => [
                'driver' => 'persistent',
                'connection' => env('AI_MEMORY_DB_CONNECTION', 'sqlite'),
                'table' => env('AI_MEMORY_TABLE', 'ai_memories'),
                'limit' => (int) env('AI_MEMORY_LIMIT', 100),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    */
    'rag' => [
        'default_chunk_strategy' => env('AI_RAG_CHUNK_STRATEGY', 'recursive'),
        'chunk_size' => (int) env('AI_RAG_CHUNK_SIZE', 1000),
        'chunk_overlap' => (int) env('AI_RAG_CHUNK_OVERLAP', 200),
        'top_k' => (int) env('AI_RAG_TOP_K', 5),
        'min_score' => (float) env('AI_RAG_MIN_SCORE', 0.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Agent Configuration
    |--------------------------------------------------------------------------
    */
    'agent' => [
        'default_max_steps' => (int) env('AI_AGENT_MAX_STEPS', 10),
        'default_llm' => env('AI_DEFAULT_LLM', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Observability
    |--------------------------------------------------------------------------
    */
    'observability' => [
        'track_cost' => (bool) env('AI_TRACK_COST', false),
        'track_tokens' => (bool) env('AI_TRACK_TOKENS', false),
        'track_latency' => (bool) env('AI_TRACK_LATENCY', false),
        'store' => env('AI_OBSERVABILITY_STORE', 'log'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => (bool) env('AI_CACHE_ENABLED', false),
        'store' => env('AI_CACHE_STORE', 'redis'),
        'ttl' => (int) env('AI_CACHE_TTL', 3600),
        'prefix' => env('AI_CACHE_PREFIX', 'ai_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => (bool) env('AI_RATE_LIMIT_ENABLED', false),
        'max_attempts' => (int) env('AI_RATE_LIMIT_MAX', 60),
        'decay_seconds' => (int) env('AI_RATE_LIMIT_DECAY', 60),
    ],
];
