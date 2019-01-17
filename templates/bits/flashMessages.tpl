{if $flashMessages}
  <div class="w-75 mx-auto">
    {foreach $flashMessages as $m}
      <div class="alert alert-{$m.type} alert-dismissible fade show" role="alert">
        {$m.text}
        <button
          type="button"
          class="close"
          data-dismiss="alert"
          aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    {/foreach}
  </div>
{/if}