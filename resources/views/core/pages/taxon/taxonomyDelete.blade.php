<div id="evaluation-message" name="evaluation-message">
    <span>
        Taxon record first needs to be evaluated before it can be deleted from the system. The evaluation ensures that the deletion of this record will not interfere with data integrity.
    </span>
</div>
@php
    
@endphp
<x-taxon-linked-item :items="$verifyArr['syn'] ?? []" title="Synonym Links" warning="Warning: synonym links exist for this taxon. They must be remapped before this taxon can be removed." item-name-plural="synonym links" />