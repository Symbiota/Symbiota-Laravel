<div id="evaluation-message" name="evaluation-message">
    <span>
        Taxon record first needs to be evaluated before it can be deleted from the system. The evaluation ensures that the deletion of this record will not interfere with data integrity.
    </span>
</div>
@php
    
@endphp
<x-taxon-linked-item :items="$verifyArr['child'] ?? []" title="Child Taxa" warning="Warning: children taxa exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="child taxa" />
<x-taxon-linked-item :items="$verifyArr['syn'] ?? []" title="Synonym Links" warning="Warning: synonym links exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="synonym links" />
<x-taxon-linked-item :items="$verifyArr['img'] ?? []" title="Image Links" warning="Warning: images exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="images" />
<x-taxon-linked-item :items="$verifyArr['map'] ?? []" title="Taxon Maps" warning="Warning: taxon maps exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="taxon maps" />
<x-taxon-linked-item :items="$verifyArr['vern'] ?? []" title="Vernaculars" warning="Warning: vernacular names exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="vernacular names" />
<x-taxon-linked-item :items="$verifyArr['tdesc'] ?? []" title="Text Descriptions" warning="Warning: text descriptions exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked text descriptions" />
<x-taxon-linked-item :items="$verifyArr['occur'] ?? []" title="Occurrence Records" warning="Warning: occurrence records exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked occurrence records" />
<x-taxon-linked-item :items="$verifyArr['dets'] ?? []" title="Determinations" warning="Warning: determinations exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked determination records" />
<x-taxon-linked-item :items="$verifyArr['cl'] ?? []" title="Checklists" warning="Warning: checklists exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked checklists" />
<x-taxon-linked-item :items="$verifyArr['kmdesc'] ?? []" title="Morphological Character Key Descriptions" warning="Warning: morphological characters exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked morphological characters" />
<x-taxon-linked-item :items="$verifyArr['link'] ?? []" title="Linked Resources" warning="Warning: linked resources exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="linked resources" />

