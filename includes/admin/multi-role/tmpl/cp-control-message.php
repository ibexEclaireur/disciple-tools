<label>
	<# if ( data.label ) { #>
		<span class="members-cp-label">{{ data.label }}</span>
	<# } #>

	<textarea name="dt_multi_role_access_error" class="widefat">{{{ data.value }}}</textarea>

	<# if ( data.description ) { #>
		<span class="members-cp-description">{{{ data.description }}}</span>
	<# } #>
</label>