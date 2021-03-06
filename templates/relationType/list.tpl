{extends "layout.tpl"}

{block "title"}{cap}{t}title-relation-types{/t}{/cap}{/block}

{block "content"}
  <div class="container my-5">
    <h1 class="mb-4">{cap}{t}title-relation-types{/t}{/cap}</h1>

    {if $numEntityTypes}
      <a class="btn btn-sm btn-primary col-12 col-md-3" href="{Router::link('relationType/edit')}">
        {include "bits/icon.tpl" i=add_circle}
        {t}link-add-relation-type{/t}
      </a>
    {else}
      {t 1=Router::link('entityType/list')}
      info-add-entity-types-before-relation-types-%1
      {/t}
    {/if}

    {if count($relationTypes)}
      <table class="table table-hover mt-5 mb-4">
        <thead>
          <tr class="small">
            <th>{t}label-name{/t}</th>
            <th>{t}label-from-entity-type{/t}</th>
            <th>{t}label-to-entity-type{/t}</th>
            <th>{t}label-weight{/t}</th>
            <th>{t}actions{/t}</th>
          </tr>
        </thead>
        <tbody>
          {foreach $relationTypes as $rt}
            <tr class="small">
              <td class="align-middle">{$rt->name|escape}</td>
              <td class="align-middle">{$rt->getFromEntityType()->name|escape}</td>
              <td class="align-middle">{$rt->getToEntityType()->name|escape}</td>
              <td class="align-middle">{$rt->weight}</td>
              <td>
                <a
                  href="{$rt->getEditUrl()}"
                  class="btn"
                  title="{t}link-edit{/t}">
                  {include "bits/icon.tpl" i=mode_edit}
                </a>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    {/if}
  </div>
{/block}
