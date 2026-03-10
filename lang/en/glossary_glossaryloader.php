<?php

return [
    'LOADER' => 'Glossary Term Loader',
    'GLOSS_MGMNT' => 'Glossary Management',
    'BATCH_LOAD' => 'Glossary Batch Loader',
    'G_BATCH_LOAD' => 'Glossary Term Batch Loader',
    'BATCH_EXPLAIN' => 'This page allows a Taxonomic Administrator to batch upload glossary data files.',
    'UPLOAD_FORM' => 'Term Upload Form',
    'SOURCE_FIELD' => 'Source Field',
    'TARGET_FIELD' => 'Target Field',
    'UNMAPPED' => 'Field Unmapped',
    'LEAVE_UNMAPPED' => 'Leave Field Unmapped',
    'TRANSFER_TERMS' => 'Transfer Terms To Central Table',
    'REVIEW_STATS' => 'Review upload statistics below before activating. Use the download option to review and/or adjust for reload if necessary.',
    'TERMS_UPLOADED' => 'Terms uploaded',
    'TOTAL_TERMS' => 'Total terms',
    'IN_DB' => 'Terms already in database',
    'NEW_TERMS' => 'New terms',
    'UNAVAILABLE' => 'Upload statistics are unavailable',
    'DOWNLOAD_TERMS' => 'Download CSV Terms File',
    'TERM_SUCCESS' => 'Terms upload appears to have been successful',
    'G_SEARCH' => 'Glossary Search',
    'TO_SEARCH' => 'page to search for a loaded name.',
    'UPLOAD_EXPLAIN' => 'Flat structured, CSV (comma delimited) text files can be uploaded here.
						Please specify the taxonomic group to which the terms will be related.
						If your file contains terms in multiple languages, label each column of terms as the language the terms are in (e.g., English),
						and then name all columns related to that term as the language, underscore, and then the column name
						(e.g., English, English_definition, Spanish, Spanish_definition, etc.). Columns can be added for the definition,
						author, translator, source, notes, and an online resource url.
						Synonyms can be added by naming the column the language, underscore, and synonym (e.g., English_synonym).
						A source can be added for all of the terms by filling in the Enter Sources box below.
						Please do not use spaces in the column names or file names.
						If the file upload step fails without displaying an error message, it is possible that the
						file size exceeds the file upload limits set within your PHP installation (see your php configuration file).',
    'ENTER_TAXON' => 'Enter Taxonomic Group',
    'ENTER_SOURCE' => 'Enter Sources',
    'UPLOAD' => 'Upload File',
];
