DROP TABLE IF EXISTS "user";
CREATE TABLE "public"."user" (
    "id" uuid NOT NULL,
    "name" character varying(255) NOT NULL,
    "authtoken" character varying(256) DEFAULT NULL,
    CONSTRAINT "user_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "user" ("id", "name", "authtoken") VALUES
('e55f1507-156b-4341-8e73-b1491a6afaf8',    'phil', 'dc68ee9ec238985083c2b1db159e13bb'),
('c4fab668-1a4b-4f87-8651-fce119b05bad',    'lolik',    '6228dfce959e94e97fbe91d7dc64d7a8'),
('e6eead4b-bb85-4dcf-bdd5-f073bec719a2',    'third user',   '9006b5720cdd876c07e49a6ed625314b');

DROP TABLE IF EXISTS "task";
CREATE TABLE "public"."task" (
    "id" integer NOT NULL,
    "title" character varying(256) NOT NULL,
    "description" text,
    "created_at" timestamp(0) NOT NULL,
    "user_id" uuid NOT NULL,
    CONSTRAINT "task_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "fk_527edb25a76ed395" FOREIGN KEY (user_id) REFERENCES "user"(id) NOT DEFERRABLE
) WITH (oids = false);

CREATE INDEX "idx_527edb25a76ed395" ON "public"."task" USING btree ("user_id");

INSERT INTO "task" ("id", "title", "description", "created_at", "user_id") VALUES
(1, 'brand new title from curl',    'loooong description from yandex referats wiil be in next issue',   '2017-08-28 21:23:05',  'e55f1507-156b-4341-8e73-b1491a6afaf8'),
(10,    'new task from curl',   'this task was created from curl cli',  '2017-08-28 21:39:09',  'e55f1507-156b-4341-8e73-b1491a6afaf8'),
(11,    'new task from curl',   'this task was created from curl cli',  '2017-08-28 21:39:20',  'e55f1507-156b-4341-8e73-b1491a6afaf8'),
(12,    'lioliks task', NULL,   '2017-01-02 00:00:00',  'c4fab668-1a4b-4f87-8651-fce119b05bad'),
(21,    'all new task title',   'all new task description ',    '2017-08-12 00:00:00',  'e55f1507-156b-4341-8e73-b1491a6afaf8');
