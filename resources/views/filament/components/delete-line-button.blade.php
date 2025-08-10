<x-filament::icon-button
    icon="heroicon-o-trash"
    color="danger"
    size="sm"
    :disabled="false"
    x-on:click="$dispatch('repeater::deleteItem', {uuid: $getStatePath()})"
/>
