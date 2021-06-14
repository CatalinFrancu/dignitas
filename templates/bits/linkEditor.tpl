{* optional argument: $links: array of Link objects *}
{hf label=$labelText}
  <button class="add-link btn btn-outline-secondary btn-sm mb-2" type="button">
    {include "bits/icon.tpl" i=add_circle}
    {$addButtonText}
  </button>

  <div class="px-0">
    <table class="table table-sm sortable">
      <tbody id="link-container">
        {include "bits/linkEditorRow.tpl" rowId="linkStem"}
        {foreach $links as $link}
          {include "bits/linkEditorRow.tpl"}
        {/foreach}
      </tbody>
    </table>

    {include "bs/feedback.tpl" errors=$errors|default:null}
  </div>
{/hf}
