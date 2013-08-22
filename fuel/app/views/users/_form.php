<?php echo Form::open(array("class"=>"form-horizontal")); ?>

	<fieldset>
		<div class="control-group">
			<?php echo Form::label('Username', 'username', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('username', Input::post('username', isset($user) ? $user->username : ''), array('class' => 'span4', 'placeholder'=>'Username')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Password', 'password', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('password', Input::post('password', isset($user) ? $user->password : ''), array('class' => 'span4', 'placeholder'=>'Password')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Level', 'level', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('level', Input::post('level', isset($user) ? $user->level : ''), array('class' => 'span4', 'placeholder'=>'Level')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Email', 'email', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('email', Input::post('email', isset($user) ? $user->email : ''), array('class' => 'span4', 'placeholder'=>'Email')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Group', 'group', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('group', Input::post('group', isset($user) ? $user->group : ''), array('class' => 'span4', 'placeholder'=>'Group')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Last login', 'last_login', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('last_login', Input::post('last_login', isset($user) ? $user->last_login : ''), array('class' => 'span4', 'placeholder'=>'Last login')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Login hash', 'login_hash', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::input('login_hash', Input::post('login_hash', isset($user) ? $user->login_hash : ''), array('class' => 'span4', 'placeholder'=>'Login hash')); ?>

			</div>
		</div>
		<div class="control-group">
			<?php echo Form::label('Profile fields', 'profile_fields', array('class'=>'control-label')); ?>

			<div class="controls">
				<?php echo Form::textarea('profile_fields', Input::post('profile_fields', isset($user) ? $user->profile_fields : ''), array('class' => 'span8', 'rows' => 8, 'placeholder'=>'Profile fields')); ?>

			</div>
		</div>
		<div class="control-group">
			<label class='control-label'>&nbsp;</label>
			<div class='controls'>
				<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>			</div>
		</div>
	</fieldset>
<?php echo Form::close(); ?>