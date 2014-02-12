<style type="text/css">
label { display:block; }

</style>

<h1>My Content</h1>
<p><?= $message; ?></p>

<form action="" method="post">
	<label for="UserFirstName">First Name:</label>
	<?= $this->form->text('User.first_name', array('style'=>'width:300px;', 'maxlength'=>30)); ?>
	<br /><br />
	<label for="UserPassword">Password:</label>
	<?= $this->form->password('User.password'); ?>
	<br /><br />
	<label for="UserLastName">Last Name:</label>
	<?= $this->form->text('Userlstname'); ?>
	<br /><br />
	
	
</form>

<pre>
<? print_r($this->data); ?>
</pre>
