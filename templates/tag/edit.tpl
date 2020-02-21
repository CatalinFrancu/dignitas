{extends "layout.tpl"}

{capture "title"}
{if $t->id}
  {t}title-edit-tag{/t}
{else}
  {t}title-add-tag{/t}
{/if}
{/capture}

{block "title"}{cap}{$smarty.capture.title}{/cap}{/block}

{block "content"}
  <h3>{$smarty.capture.title}</h3>

  {include "bits/tagAncestors.tpl" tag=$t}

  <form class="form-horizontal" method="post">
    <input type="hidden" name="id" value="{$t->id}">

    <div class="form-group">
      <label for="value" class="col-2 control-label">
        {t}label-name{/t}
      </label>
      <div class="col-10">
        <div>
          <input type="text"
            class="form-control {if isset($errors.value)}is-invalid{/if}"
            id="value"
            name="value"
            value="{$t->value}">
          {include "bits/fieldErrors.tpl" errors=$errors.value|default:null}
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="tooltip" class="col-2 control-label">
        {t}label-details{/t}
      </label>
      <div class="col-10">
        <div>
          <input type="text"
            class="form-control"
            id="tooltip"
            name="tooltip"
            value="{$t->tooltip}"
            placeholder="{t}label-optional-tooltip{/t}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="parentId" class="col-2 control-label">
        {t}label-parent-tag{/t}
      </label>
      <div class="col-10">
        <div>
          <select
            id="parentId"
            name="parentId"
            class="form-control {if isset($errors.parentId)}is-invalid{/if}">
            {if $t->parentId}
              <option value="{$t->parentId}" selected></option>
            {/if}
          </select>
          {include "bits/fieldErrors.tpl" errors=$errors.parentId|default:null}
        </div>

      </div>
    </div>

    <div class="form-group"">
      <label for="color" class="col-2 control-label">
        {t}label-text-color{/t}
      </label>
      <div class="col-10">
        <div class="input-group colorpicker-component">
          <span class="input-group-prepend input-group-text colorpicker-input-addon">
            <i></i>
          </span>
          <input type="text"
            class="form-control"
            id="color"
            name="color"
            value="{$t->getColor()}">
        </div>
      </div>
    </div>

    <div class="form-group"">
      <label for="background" class="col-2 control-label">
        {t}label-background-color{/t}
      </label>
      <div class="col-10">
        <div class="input-group colorpicker-component">
          <span class="input-group-prepend input-group-text colorpicker-input-addon">
            <i></i>
          </span>
          <input type="text"
            class="form-control"
            id="background"
            name="background"
            value="{$t->getBackground()}">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="icon" class="col-2 control-label">
        {t}label-icon{/t}
      </label>
      <div class="col-10">
        <div class="input-group">
          {if $t->icon}
            <span class="input-group-prepend input-group-text">
              <i class="icon icon-{$t->icon}"></i>
            </span>
          {/if}
          <input type="text"
            class="form-control"
            id="icon"
            name="icon"
            value="{$t->icon}">
        </div>

        <div class="checkbox">
          <label>
            <input type="checkbox"
              name="iconOnly"
              value="1"
              {if $t->iconOnly}checked{/if}>
            {t}label-icon-only{/t}
          </label>
        </div>

        <small class="form-text text-muted">
          {t
            1="https://github.com/CatalinFrancu/dignitas/blob/master/www/css/third-party/fontello/css/icons.css"
            2="http://fontello.com/"}
          info-tag-icon-name
          {/t}
        </small>

      </div>
    </div>

    <button name="saveButton" type="submit" class="btn btn-primary">
      <i class="icon icon-floppy"></i>
      {t}link-save{/t}
    </button>

    <a href="" class="btn btn-light">
      <i class="icon icon-cancel"></i>
      {t}link-cancel{/t}
    </a>

    {if $t->id}
      <a class="btn btn-light" href="{Router::link('tag/view')}/{$t->id}">
        <i class="icon icon-left"></i>
        {t}link-back-to-tag{/t}
      </a>
    {/if}

    {if $t->id && User::may(User::PRIV_DELETE_TAG)}
      <button
        name="deleteButton"
        type="submit"
        class="btn btn-danger float-right"
        data-confirm="{t}info-confirm-delete-tag{/t}"
        {if !$canDelete}
        disabled
        title="{t}info-cannot-delete-tag{/t}"
        {/if}
      >
        <i class="icon icon-trash"></i>
        {t}link-delete{/t}
      </button>
    {/if}
  </form>

  {if count($children)}
    <h3>{t}title-direct-descendants{/t}</h3>

    {foreach $children as $c}
      {include "bits/tag.tpl" t=$c link=true}
    {/foreach}
  {/if}

  {if count($homonyms)}
    <h3>{t}title-duplicate-tags{/t}</h3>

    {foreach $homonyms as $h}
      <div>
        {include "bits/tagAncestors.tpl" tag=$h}
      </div>
    {/foreach}
  {/if}

  {* frequent colors to be used by the color pickers *}
  {foreach $frequentColors as $color => $list}
    <div id="frequent-{$color}">
      {foreach $list as $color}
        <div>{$color}</div>
      {/foreach}
    </div>
  {/foreach}
{/block}
