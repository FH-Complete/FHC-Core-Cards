CREATE SEQUENCE IF NOT EXISTS extension.tbl_cards_terminal_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

GRANT SELECT, UPDATE ON SEQUENCE extension.tbl_cards_terminal_id_seq TO vilesci;

CREATE TABLE IF NOT EXISTS extension.tbl_cards_terminal
(
    cardsterminal_id    integer         NOT NULL DEFAULT NEXTVAL('extension.tbl_cards_terminal_id_seq'::regclass),
    name                varchar(64)     NOT NULL,
    beschreibung        varchar(256)    NOT NULL,
    ort                 varchar(256)    NOT NULL,
    aktiv               boolean         NOT NULL,
    type                varchar(32)     NOT NULL,
    insertvon           varchar(32)     NOT NULL,
    insertamum          timestamp without time zone default now(),
    updateamum          timestamp without time zone,
    updatevon           varchar(32)
);

DO $$
    BEGIN
        ALTER TABLE extension.tbl_cards_terminal ADD CONSTRAINT tbl_cards_terminal_pkey PRIMARY KEY (cardsterminal_id);
    EXCEPTION WHEN OTHERS THEN NULL;
END $$;


GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE extension.tbl_cards_terminal TO vilesci;

