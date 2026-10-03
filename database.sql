CREATE TABLE IF NOT EXISTS calls (id UUID PRIMARY KEY, caller_user_id UUID NOT NULL, callee_chat_id UUID, type VARCHAR(16) NOT NULL, status VARCHAR(16) NOT NULL, started_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), ended_at TIMESTAMPTZ, created_at TIMESTAMPTZ NOT NULL DEFAULT NOW());
CREATE INDEX IF NOT EXISTS calls_caller_idx ON calls(caller_user_id, created_at DESC);
