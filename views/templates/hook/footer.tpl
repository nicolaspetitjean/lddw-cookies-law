{**
* 2018 http://www.la-dame-du-web.com
*
* @author Nicolas PETITJEAN <n.petitjean@la-dame-du-web.com>
* @copyright 2018 Nicolas PETITJEAN
* @license MIT License
*}
{if !$cookie_setted}
<div id="lddw-cookie-modal-box" class="lddw-cookies-notice{if $layout == 'fullwidth'} lddw-cookies-notice-banner{/if}">
    <span class="lddw-cookie-close">x</span>
    <div class="lddw-cookie-title">{$title|escape:'html':'UTF-8'}</div>
    <p>{$message}</p>
    <p class="lddw-cookie-buttons">
        <button class="lddw-button"
                id="lddw-cookie-agree">{$text_button|escape:'html':'UTF-8'}</button>
        <a class="lddw-button" id="lddw-cookie-more"
           href="{$url}">{$text_more|escape:'html':'UTF-8'}</a>
    </p>
</div>
{/if}
{literal}
    <script>
		window.lddw_cookieslaw = {
			expire: '{/literal}{$expiry}{literal}',
			domain: '{/literal}{$domain}{literal}',
			direction: '{/literal}{$direction}{literal}'
		}
    </script>
{/literal}