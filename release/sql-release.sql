use zzz;

CREATE TABLE dictionary(
   id     INTEGER  NOT NULL PRIMARY KEY
  ,tag    VARCHAR(4) NOT NULL
  ,description VARCHAR(55) NOT NULL
);
INSERT INTO dictionary(id,tag,description) VALUES (1,'.','sentence (. ; ? *)');
INSERT INTO dictionary(id,tag,description) VALUES (2,'(','left paren');
INSERT INTO dictionary(id,tag,description) VALUES (3,')','right paren');
INSERT INTO dictionary(id,tag,description) VALUES (4,'*','not, n''t');
INSERT INTO dictionary(id,tag,description) VALUES (5,'--','dash');
INSERT INTO dictionary(id,tag,description) VALUES (6,',','comma');
INSERT INTO dictionary(id,tag,description) VALUES (7,':','colon');
INSERT INTO dictionary(id,tag,description) VALUES (8,'ABL','pre-qualifier (quite, rather)');
INSERT INTO dictionary(id,tag,description) VALUES (9,'ABN','pre-quantifier (half, all)');
INSERT INTO dictionary(id,tag,description) VALUES (10,'ABX','pre-quantifier (both)');
INSERT INTO dictionary(id,tag,description) VALUES (11,'AP','post-determiner (many, several, next)');
INSERT INTO dictionary(id,tag,description) VALUES (12,'AT','article (a, the, no)');
INSERT INTO dictionary(id,tag,description) VALUES (13,'BE','be');
INSERT INTO dictionary(id,tag,description) VALUES (14,'BED','were');
INSERT INTO dictionary(id,tag,description) VALUES (15,'BEDZ','was');
INSERT INTO dictionary(id,tag,description) VALUES (16,'BEG','being');
INSERT INTO dictionary(id,tag,description) VALUES (17,'BEM','am');
INSERT INTO dictionary(id,tag,description) VALUES (18,'BEN','been');
INSERT INTO dictionary(id,tag,description) VALUES (19,'BER','are, art');
INSERT INTO dictionary(id,tag,description) VALUES (20,'BEZ','is');
INSERT INTO dictionary(id,tag,description) VALUES (21,'CC','coordinating conjunction (and, or)');
INSERT INTO dictionary(id,tag,description) VALUES (22,'CD','cardinal numeral (one, two, 2, etc.)');
INSERT INTO dictionary(id,tag,description) VALUES (23,'CS','subordinating conjunction (if, although)');
INSERT INTO dictionary(id,tag,description) VALUES (24,'DO','do');
INSERT INTO dictionary(id,tag,description) VALUES (25,'DOD','did');
INSERT INTO dictionary(id,tag,description) VALUES (26,'DOZ','does');
INSERT INTO dictionary(id,tag,description) VALUES (27,'DT','singular determiner/quantifier (this, that)');
INSERT INTO dictionary(id,tag,description) VALUES (28,'DTI','singular or plural determiner/quantifier (some, any)');
INSERT INTO dictionary(id,tag,description) VALUES (29,'DTS','plural determiner (these, those)');
INSERT INTO dictionary(id,tag,description) VALUES (30,'DTX','determiner/double conjunction (either)');
INSERT INTO dictionary(id,tag,description) VALUES (31,'EX','existential there');
INSERT INTO dictionary(id,tag,description) VALUES (32,'FW','foreign word (hyphenated before regular tag)');
INSERT INTO dictionary(id,tag,description) VALUES (33,'HV','have');
INSERT INTO dictionary(id,tag,description) VALUES (34,'HVD','had (past tense)');
INSERT INTO dictionary(id,tag,description) VALUES (35,'HVG','having');
INSERT INTO dictionary(id,tag,description) VALUES (36,'HVN','had (past participle)');
INSERT INTO dictionary(id,tag,description) VALUES (37,'IN','preposition');
INSERT INTO dictionary(id,tag,description) VALUES (38,'JJ','adjective');
INSERT INTO dictionary(id,tag,description) VALUES (39,'JJR','comparative adjective');
INSERT INTO dictionary(id,tag,description) VALUES (40,'JJS','semantically superlative adjective (chief, top)');
INSERT INTO dictionary(id,tag,description) VALUES (41,'JJT','morphologically superlative adjective (biggest)');
INSERT INTO dictionary(id,tag,description) VALUES (42,'MD','modal auxiliary (can, should, will)');
INSERT INTO dictionary(id,tag,description) VALUES (43,'NC','cited word (hyphenated after regular tag)');
INSERT INTO dictionary(id,tag,description) VALUES (44,'NN','singular or mass noun');
INSERT INTO dictionary(id,tag,description) VALUES (45,'NN$','possessive singular noun');
INSERT INTO dictionary(id,tag,description) VALUES (46,'NNS','plural noun');
INSERT INTO dictionary(id,tag,description) VALUES (47,'NNS$','possessive plural noun');
INSERT INTO dictionary(id,tag,description) VALUES (48,'NP','proper noun or part of name phrase');
INSERT INTO dictionary(id,tag,description) VALUES (49,'NP$','possessive proper noun');
INSERT INTO dictionary(id,tag,description) VALUES (50,'NPS','plural proper noun');
INSERT INTO dictionary(id,tag,description) VALUES (51,'NPS$','possessive plural proper noun');
INSERT INTO dictionary(id,tag,description) VALUES (52,'NR','adverbial noun (home, today, west)');
INSERT INTO dictionary(id,tag,description) VALUES (53,'OD','ordinal numeral (first, 2nd)');
INSERT INTO dictionary(id,tag,description) VALUES (54,'PN','nominal pronoun (everybody, nothing)');
INSERT INTO dictionary(id,tag,description) VALUES (55,'PN$','possessive nominal pronoun');
INSERT INTO dictionary(id,tag,description) VALUES (56,'PP$','possessive personal pronoun (my, our)');
INSERT INTO dictionary(id,tag,description) VALUES (57,'PP$$','second (nominal) possessive pronoun (mine, ours)');
INSERT INTO dictionary(id,tag,description) VALUES (58,'PPL','singular reflexive/intensive personal pronoun (myself)');
INSERT INTO dictionary(id,tag,description) VALUES (59,'PPLS','plural reflexive/intensive personal pronoun (ourselves)');
INSERT INTO dictionary(id,tag,description) VALUES (60,'PPO','objective personal pronoun (me, him, it, them)');
INSERT INTO dictionary(id,tag,description) VALUES (61,'PPS','3rd. singular nominative pronoun (he, she, it, one)');
INSERT INTO dictionary(id,tag,description) VALUES (62,'PPSS','other nominative personal pronoun (I, we, they, you)');
INSERT INTO dictionary(id,tag,description) VALUES (63,'PRP','Personal pronoun');
INSERT INTO dictionary(id,tag,description) VALUES (64,'PRP$','Possessive pronoun');
INSERT INTO dictionary(id,tag,description) VALUES (65,'QL','qualifier (very, fairly)');
INSERT INTO dictionary(id,tag,description) VALUES (66,'QLP','post-qualifier (enough, indeed)');
INSERT INTO dictionary(id,tag,description) VALUES (67,'RB','adverb');
INSERT INTO dictionary(id,tag,description) VALUES (68,'RBR','comparative adverb');
INSERT INTO dictionary(id,tag,description) VALUES (69,'RBT','superlative adverb');
INSERT INTO dictionary(id,tag,description) VALUES (70,'RN','nominal adverb (here, then, indoors)');
INSERT INTO dictionary(id,tag,description) VALUES (71,'RP','adverb/particle (about, off, up)');
INSERT INTO dictionary(id,tag,description) VALUES (72,'TO','infinitive marker to');
INSERT INTO dictionary(id,tag,description) VALUES (73,'UH','interjection, exclamation');
INSERT INTO dictionary(id,tag,description) VALUES (74,'VB','verb, base form');
INSERT INTO dictionary(id,tag,description) VALUES (75,'VBD','verb, past tense');
INSERT INTO dictionary(id,tag,description) VALUES (76,'VBG','verb, present participle/gerund');
INSERT INTO dictionary(id,tag,description) VALUES (77,'VBN','verb, past participle');
INSERT INTO dictionary(id,tag,description) VALUES (78,'VBP','verb, non 3rd person, singular, present');
INSERT INTO dictionary(id,tag,description) VALUES (79,'VBZ','verb, 3rd. singular present');
INSERT INTO dictionary(id,tag,description) VALUES (80,'WDT','wh- determiner (what, which)');
INSERT INTO dictionary(id,tag,description) VALUES (81,'WP$','possessive wh- pronoun (whose)');
INSERT INTO dictionary(id,tag,description) VALUES (82,'WPO','objective wh- pronoun (whom, which, that)');
INSERT INTO dictionary(id,tag,description) VALUES (83,'WPS','nominative wh- pronoun (who, which, that)');
INSERT INTO dictionary(id,tag,description) VALUES (84,'WQL','wh- qualifier (how)');
INSERT INTO dictionary(id,tag,description) VALUES (85,'WRB','wh- adverb (how, where, when)');


CREATE TABLE `dictionary_to_lexic` (
    `tag_id`INT(11) NOT NULL,
    `word_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`tag_id`, `word_id`),
    CONSTRAINT `Constr_Word_Tag_fk`
        FOREIGN KEY `Word_FK` (`word_id`) REFERENCES `lexic` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB CHARACTER SET ascii COLLATE ascii_general_ci