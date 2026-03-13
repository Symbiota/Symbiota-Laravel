<?php

return [
    'IMG_LOADER' => 'Image Loader',
    'IMG_IMPORTER' => 'Image Importer',
    'IMG_UPLOAD_FORM' => 'Image Upload Form',
    'IMG_UPLOAD_EXPLAIN' => 'This tool is designed to aid collection managers in batch importing image files
					that are defined within a comma delimited text file (CSV). The only two required fields are
					the image url. If scientific name is null, script will attempt to extract taxon name from image file name.
					The image urls must represent the full path to the image, or consist of the file names with base path
					defined within the ingestion form.
					Other optional fields include: creator, caption, locality, sourceUrl, anatomy,
					notes, collection identifier, owner, copyright, sortSequence.
					Internal fields can include creatorUid, occid, or tid.',
    'UPLOAD_FILE' => 'Upload File',
    'ANALYZE_INPUT_FILE' => 'Analyze Input File',
    'SELECT_TARGET' => 'Select Target',
    'FIELDS_YELLOW' => 'Fields in yellow are not yet mapped or verified',
    'LRG_IMG' => 'Large Image',
    'LEAVE_BLANK' => 'Leave blank',
    'MAP_REMOTE_IMGS' => 'Map to remote images',
    'IMPORT_LOCAL' => 'Import to local storage',
    'BASE_PATH' => 'Base Path',
    'UPLOAD_IMGS' => 'Upload Images',
];
