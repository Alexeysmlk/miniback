SELECT 'CREATE DATABASE blog_db_testing'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'blog_db_testing')\gexec
