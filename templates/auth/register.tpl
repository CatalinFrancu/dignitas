{extends "layout.tpl"}

{block "title"}{cap}{t}sign up{/t}{/cap}{/block}

{block "content"}
  <div class="card w-50 m-auto">
    <div class="card-header">
      {cap}{t}sign up{/t}{/cap}
    </div>

    <div class="card-body">
      <form method="post">

        <div class="form-group">
          <div class="input-group">
            <span class="input-group-prepend">
              <i class="input-group-text icon icon-user"></i>
            </span>
            <input
              class="form-control {if isset($errors.nickname)}is-invalid{/if}"
              type="text"
              name="nickname"
              value="{$nickname|escape}"
              autofocus
              placeholder="{t}nickname{/t}">
          </div>
          {include "bits/fieldErrors.tpl" errors=$errors.nickname|default:null}
        </div>

        <div class="form-group">
          <div class="input-group">
            <span class="input-group-prepend">
              <i class="input-group-text icon icon-mail"></i>
            </span>
            <input
              class="form-control {if isset($errors.email)}is-invalid{/if}"
              type="email"
              name="email"
              value="{$email|escape}"
              placeholder="{t}email{/t}">
          </div>
          {include "bits/fieldErrors.tpl" errors=$errors.email|default:null}
        </div>

        <div class="form-group">
          <div class="input-group">
            <span class="input-group-prepend">
              <i class="input-group-text icon icon-lock"></i>
            </span>
            <input
              class="form-control {if isset($errors.password)}is-invalid{/if}"
              type="password"
              name="password"
              value="{$password|escape}"
              placeholder="{t}password{/t}">
          </div>
          {include "bits/fieldErrors.tpl" errors=$errors.password|default:null}
        </div>

        <div class="form-group">
          <div class="input-group">
            <span class="input-group-prepend">
              <i class="input-group-text icon icon-lock"></i>
            </span>
            <input
              class="form-control"
              type="password"
              name="password2"
              value="{$password2|escape}"
              placeholder="{t}password (again){/t}">
          </div>
        </div>

        <div class="form-check">
          <label>
            <input
              type="checkbox"
              class="form-check-input"
              name="remember"
              value="1"
              {if $remember}checked{/if}>
            {t}remember me for one year{/t}
          </label>
        </div>

        <div>
          <button class="btn btn-primary" type="submit" name="submitButton">
            {t}sign up{/t}
          </button>

          <a class="btn btn-link float-right" href="{Router::link('auth/login')}">
            {t}I already have an account{/t}
          </a>
        </div>
      </form>
    </div>
  </div>

{/block}
