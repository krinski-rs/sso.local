-- DROP SCHEMA IF EXISTS "authorization" CASCADE;
CREATE SCHEMA "authorization";

DROP TYPE IF EXISTS "authorization".grant_type;
CREATE TYPE "authorization".grant_type AS ENUM ('authorization_code', 'password', 'client_credentials', 'refresh_token');

DROP TYPE IF EXISTS "authorization".user_type;
CREATE TYPE "authorization".user_type AS ENUM ('USER', 'APLICATION');
