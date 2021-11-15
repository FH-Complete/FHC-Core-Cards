CREATE TABLE IF NOT EXISTS extension.tbl_cards_print
(
    uid         varchar (32) not null,
    zugangscode varchar(64)  not null,
    pin         smallint     not null,
    insertamum  timestamp
);

DO $$
    BEGIN
        ALTER TABLE ONLY extension.tbl_cards_print ADD CONSTRAINT tbl_cards_print_benutzer_uid_fkey FOREIGN KEY (uid) REFERENCES public.tbl_benutzer(uid) ON DELETE RESTRICT ON UPDATE CASCADE;
    EXCEPTION WHEN OTHERS THEN NULL;
END $$;

CREATE UNIQUE INDEX IF NOT EXISTS tbl_cards_print_zugangscode_uindex ON extension.tbl_cards_print (zugangscode);

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE extension.tbl_cards_print TO vilesci;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE extension.tbl_cards_print TO web;
