<?php

return [
    'EDIT_METADATA' => 'Edit Collection Metadata & Contacts',
    'COLLECTION_METADATA_EDITOR' => 'Collection Metadata Editor Tab',
    'ADD_SUCCESS' => 'New collection added successfully',
    'ADD_STUFF' => 'Add contacts, resource links, or institution address below',
    'COL_PROFS' => 'Collection Profiles',
    'META_EDIT' => 'Metadata Editor',
    'CREATE_COLL' => 'Create New Collection Profile',
    'COL_META_EDIT' => 'Collection Metadata Editor',
    'CONT_RES' => 'Contacts & Resources',
    'COL_INFO' => 'Collection Information',
    'MORE_INST_CODE' => 'More information about Institution Code',
    'NAME_ONE' => 'The name (or acronym) in use by the institution having custody of the occurrence records. This field is required. For more details, see',
    'DWC_DEF' => 'Darwin Core definition',
    'MORE_COLL_CODE' => 'More information about Collection Code',
    'NAME_ACRO' => 'The name, acronym, or code identifying the collection or data set from which the record was derived. This field is optional. For more details, see',
    'COLL_NAME' => 'Collection Name',
    'DESC' => 'Description (2000 character max)',
    'HOMEPAGE' => 'Homepage',
    'CONTACT' => 'Contact',
    'CATEGORY' => 'Category',
    'NO_CATEGORY' => 'No Category',
    'ALLOW_PUBLIC_EDITS' => 'Allow Public Edits',
    'MORE_PUB_EDITS' => 'More information about Public Edits',
    'EXPLAIN_PUBLIC' => 'Checking public edits will allow any user logged into the system to modify specimen records
					and resolve errors found within the collection. However, if the user does not have explicit
					authorization for the given collection, edits will not be applied until they are
					reviewed and approved by collection administrator.',
    'LICENSE' => 'License',
    'MORE_INFO_RIGHTS' => 'More information about Rights',
    'ORPHANED' => 'orphaned term',
    'LEGAL_DOC' => 'A legal document giving official permission to do something with the resource.
					This field can be limited to a set of values by modifying the portal\'s central configuration file.
					For more details, see',
    'MORE_INFO_RIGHTS_H' => 'More information about Rights Holder',
    'HOLDER_DEF' => 'The organization or person managing or owning the rights of the resource.
					For more details, see',
    'MORE_INFO_ACCESS_RIGHTS' => 'More information about Access Rights',
    'ACCESS_DEF' => 'Information or a URL link to page with details explaining
					how one can use the data. See',
    'DATASET_TYPE' => 'Dataset Type',
    'PRES_SPECS' => 'Preserved Specimens',
    'FOSSIL_SPECS' => 'Fossil Specimens',
    'FOSSIL_WARN_1' => 'Selecting “Fossil Specimen” will activate the Paleo Module for this collection and set the default value to “FossilSpecimen” for',
    'FOSSIL_WARN_2' => 'Only choose this option if you intend to use this Collection Profile to catalog fossils.',
    'FOSSIL_WARN_3' => 'Additional features may require activation to make your fossil specimen data publicly discoverable in this portal.
						Contact your Portal Manager for more information.',
    'OBSERVATIONS' => 'Observations',
    'PERS_OBS_MAN' => 'Personal Observation Management',
    'MORE_COL_TYPE' => 'More information about Collection Type',
    'COL_TYPE_DEF' => 'Preserved Specimens signify a collection type that contains physical samples that are
						available for inspection by researchers and taxonomic experts. Use Observations when the record is not based on a physical specimen.
						Personal Observation Management is a dataset where registered users
						can independently manage their own subset of records. Records entered into this dataset are explicitly linked to the user&apos;s profile
						and can only be edited by them. This type of collection
						is typically used by field researchers to manage their collection data and print labels
						prior to depositing the physical material within a collection. Even though personal collections
						are represented by a physical sample, they are classified as &quot;observations&quot; until the
						physical material is publicly available within a collection.',
    'MANAGEMENT' => 'Management',
    'SNAPSHOT' => 'Snapshot',
    'LIVE_DATA' => 'Live Data',
    'AGGREGATE' => 'Aggregate',
    'MORE_INFO_TYPE' => 'More information about Management Type',
    'SNAPSHOT_DEF' => 'Use Snapshot when there is a separate in-house database maintained in the collection and the dataset
						within the Symbiota portal is only a periodically updated snapshot of the central database.
						A Live dataset is when the data is managed directly within the portal and the central database is the portal data.',
    'GUID_SOURCE' => 'GUID source',
    'MORE_INFO_GUID' => 'More information about Global Unique Identifier',
    'SYMB_GUID' => 'Symbiota Generated GUID (UUID)',
    'OCCID_DEF_1' => 'Occurrence Id is generally used for
						Snapshot datasets when a Global Unique Identifier (GUID) field
						is supplied by the source database (e.g. Specify database) and the GUID is mapped to the',
    'OCCURRENCEID' => 'occurrenceId',
    'OCCID_DEF_2' => 'field. The use of the Occurrence Id as the GUID is not recommended for live datasets.
						Catalog Number can be used when the value within the catalog number field is globally unique.
						The Symbiota Generated GUID (UUID) option will trigger the Symbiota data portal to automatically
						generate UUID GUIDs for each record. This option is recommended for many for Live Datasets
						but not allowed for Snapshot collections that are managed in local management system.',
    'PUBLISH_TO_AGGS' => 'Enable Publishing to Aggregators',
    'MORE_INFO_AGGREGATORS' => 'More information about Publishing to Aggregators',
    'ACTIVATE_GBIF' => 'Activates GBIF publishing tools available within Darwin Core Archive Publishing menu option',
    'SOURCE_REC_URL' => 'Source Record URL',
    'DYNAMIC_LINK_REC' => 'Dynamic link to source database individual record page',
    'MORE_INFO_SOURCE' => 'More information about Source Records URL',
    'ADVANCE_SETTING' => 'Advance setting: Adding a
						URL template here will insert a link to the source record within the specimen details page.
						An optional URL title can be include with a colon delimiting the title and URL.
						For example, &quot;SEINet source record',
    'ADVANCE_SETTING_2' => 'will display the ID with the url pointing to the original
						record managed within SEINet. Or',
    'ADVANCE_SETTING_3' => 'can be used for an	iNaturalist import if you mapped their ID field as the source
						Identifier (e.g. dbpk) during import. Template patterns --CATALOGNUMBER--, --OTHERCATALOGNUMBERS--, and --OCCURRENCEID-- are additional options.',
    'ICON_URL' => 'Icon URL',
    'WHAT_ICON' => 'What is an Icon?',
    'UPLOAD_ICON' => 'Upload an icon image file or enter the URL of an image icon that represents the collection. If entering the URL of an image already located
						on a server, click on &quot;Enter URL&quot;. The URL path can be absolute or relative. The use of icons are optional.',
    'ENTER_URL' => 'Enter URL',
    'MORE_SORTING' => 'More information about Sorting',
    'LEAVE_IF_ALPHABET' => 'Leave this field empty if you want the collections to sort alphabetically (default)',
    'COLLECTION_ID' => 'Collection ID (GUID)',
    'EXPLAIN_COLLID' => 'Global Unique Identifier for this collection (see',
    'DWC_COLLID' => 'dwc:collectionID',
    'EXPLAIN_COLLID_2' => 'If your collection already has a previously assigned GUID, that identifier should be represented here.
						For physical specimens, the recommended best practice is to use an identifier from a collections registry such as the
						Global Registry of Biodiversity Repositories',
    'SECURITY_KEY' => 'Security Key',
    'RECORDID' => 'recordID',
    'CREATE_COLL_2' => 'Create New Collection',
    'TINYMCE_INFO' => 'Collection description. You can access the text editing toolbar for this description
						by pressing key combination ALT + F10 on Windows or OPTION + F10 on MacOS.',
];
