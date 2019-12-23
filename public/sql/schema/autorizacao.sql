-- DROP SCHEMA IF EXISTS "authorization" CASCADE;
CREATE SCHEMA "authorization";

CREATE TABLE "authorization".rule (
    id serial NOT NULL,
    name character varying(100) NOT NULL,
    registration_date timestamp(0) without time zone DEFAULT now() NOT NULL,
    active boolean DEFAULT true,
    removal_date timestamp(0) without time zone DEFAULT NULL,
	CONSTRAINT rule_pk PRIMARY KEY (id)
);

CREATE TABLE "authorization".user (
    id serial NOT NULL,
    name character varying(255) NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(100) NOT NULL,
    salt character varying(100) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    grant_type "authorization".grant_type NOT NULL DEFAULT 'password'::"authorization".grant_type,
    user_type "authorization".user_type NOT NULL DEFAULT 'USER'::"authorization".user_type,
    registration_date timestamp(0) without time zone DEFAULT now() NOT NULL,
    expiration_date timestamp(0) without time zone DEFAULT NULL,
    removal_date timestamp(0) without time zone DEFAULT NULL,
	CONSTRAINT user_pk PRIMARY KEY (id),
    CONSTRAINT user_username_un UNIQUE (username)
);

CREATE TABLE "authorization".user_rule (
    id_user integer NOT NULL,
    id_rule integer NOT NULL,
    value integer NOT NULL,
    registration_date timestamp(0) without time zone DEFAULT now() NOT NULL,
    removal_date timestamp(0) without time zone DEFAULT NULL,
	CONSTRAINT user_rule_pk PRIMARY KEY (id_user, id_rule),
	CONSTRAINT user_rule_user_fk FOREIGN KEY (id_user) 
		REFERENCES "authorization".user(id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
	CONSTRAINT user_rule_rule_fk FOREIGN KEY (id_rule) 
		REFERENCES "authorization".rule(id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
	CONSTRAINT user_rule_check CHECK ((value >= 1 AND value <= 15) OR value = NULL::integer) NOT VALID
);
COMMENT ON TABLE "authorization".user_rule
    IS 'value
null	:	---- (no permission)
1		:	r--- (read only)
2		:	-w-- (writing only)
4		:	--e- (edition only)
8		:	---d (delete only)';