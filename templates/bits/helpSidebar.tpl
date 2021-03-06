{$activeCategoryId=$activeCategoryId|default:null}
{$activePageId=$activePageId|default:null}
<div class="help-sidebar ps-2 py-3">
  {foreach HelpCategory::loadAll() as $c}

    {$active=($c->id == $activeCategoryId)}
    <h6 class="help-sidebar-category {if $active}active{/if} mb-0 mt-2 fw-bold">
      <a {if !$active}href="{$c->getViewUrl()}"{/if}>{$c->getName()}</a>
    </h6>

    {foreach $c->getPages() as $p}
      {$active=($p->id == $activePageId)}
      <div class="help-sidebar-page {if $active}active{/if}">
        <a {if !$active}href="{$p->getViewUrl()}"{/if}>{$p->getTitle()}</a>
      </div>
    {/foreach}

  {/foreach}
</div>
