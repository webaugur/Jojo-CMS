{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" />
{$value}
{else}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" onchange="validate(this,this.value,'{$fd_type}')"  title="{$fd_help}" />
<img src="images/cms/incrementup.gif" style="border: 0; cursor: pointer;" alt="Increase value" onclick="$('#fm_{$fd_field}').val(isNaN($('#fm_{$fd_field}').val()) || $('#fm_{$fd_field}').val()=='' ? '0' : parseInt($('#fm_{$fd_field}').val())+1);" />
<img src="images/cms/incrementdown.gif" style="border: 0; cursor: pointer;" alt="Decrease value" onclick="$('#fm_{$fd_field}').val(isNaN($('#fm_{$fd_field}').val()) || $('#fm_{$fd_field}').val()=='' || $('#fm_{$fd_field}').val()=='0' ? '0' : parseInt($('#fm_{$fd_field}').val())-1);" />
{/if}