<?php

class RedisCache {
    private $redis;
    private $host;
    private $port;
    private $password;
    private $ttl; // Default time-to-live for cache keys in seconds

    public function __construct($host = '127.0.0.1', $port = 6379, $ttl = 3600, $password=null) {
        $this->host = $host;
        $this->port = $port;
        $this->ttl = $ttl;
        
        if(!empty($password))
        {
            $this->password = $password;
        }

        $this->redis = new Redis();
        $this->connect();
    }

    // Connect to Redis server
    private function connect() {
        try {
            $this->redis->connect($this->host, $this->port);
            if(isset($this->password))
            {
                if (!$this->redis->auth($this->password)) {
                    throw new Exception('Redis authentication failed.');
                }
            }
            
        } catch (Exception $e) {
            echo "Couldn't connect to Redis: " . $e->getMessage();
        }
    }

    // Set cache value with a key and TTL (in seconds)
    public function set($key, $value, $ttl = null) {
        if ($ttl === null) {
            $ttl = $this->ttl;
        }
        return $this->redis->setex($key, $ttl, $value);
    }

    // Get cache value by key
    public function get($key) {
        return $this->redis->get($key);
    }

    // Delete cache by key
    public function delete($key) {
        return $this->redis->del($key);
    }

    // Check if key exists
    public function exists($key) {
        return $this->redis->exists($key);
    }

    // Set multiple key-value pairs
    public function setMultiple(array $data, $ttl = null) {
        foreach ($data as $key => $value) {
            $this->set($key, $value, $ttl);
        }
    }

    // Get multiple values by keys
    public function getMultiple(array $keys) {
        return $this->redis->mget($keys);
    }
    // Store a multi-dimensional array
    public function setArray($key, array $value, $ttl = null) {
        if ($ttl === null) {
            $ttl = $this->ttl;
        }
        // Serialize the array before storing it
        $serializedValue = serialize($value);
        return $this->redis->setex($key, $ttl, $serializedValue);
    }

    // Retrieve and unserialize the array
    public function getArray($key) {
        $serializedValue = $this->redis->get($key);
        if ($serializedValue !== false) {
            return unserialize($serializedValue);
        }
        return null;
    }

    // Clear all keys in the Redis database
    public function clearAll() {
        return $this->redis->flushDB();
    }

    // Increment a value by a specific amount
    public function increment($key, $by = 1) {
        return $this->redis->incrBy($key, $by);
    }

    // Decrement a value by a specific amount
    public function decrement($key, $by = 1) {
        return $this->redis->decrBy($key, $by);
    }

    // Disconnect from Redis server
    public function disconnect() {
        return $this->redis->close();
    }
}
