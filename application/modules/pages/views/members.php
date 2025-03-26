<div class="container text-center">

	<div><?php echo $this->session->flashdata('message'); ?></div>
	<table class="table">
		<thead class="thead-dark">
		<tr>
			<th scope="col">Name</th>
			<th scope="col">Email</th>
			<th scope="col">Phone</th>
			<th scope="col">Password</th>
			<th scope="col"></th>
			<th scope="col"></th>
		</tr>
		</thead>
		<tbody>
		<?php log_message("ERROR", print_r($users, true)) ?>
		<?php foreach ($users as $user) : ?>
			<tr>
				<form action="" method="POST" class="col-xs-60 col-sm-35 center-block">
					<input type="hidden" name="ref" id="ref" value="update_user">
					<input type="hidden" name="user_id" id="user_id" value="<?= $user->id ?>">
					<td><input type="text" name="name" value="<?= $user->name ?>" placeholder="Full Name*" title="Full Name*" required="required" /></td>
					<td><input type="email" name="email" value="<?= $user->email ?>" placeholder="Email*" title="Email*" required="required" /></td>
					<td><input type="text" name="phone" value="<?= $user->phone ?>" placeholder="Phone" title="Phone" /></td>
					<td><input type="password" name="password" value="" placeholder="Password" title="Password"/></td>
					<td><button class="button">Update</button></td>
				</form>
				<form action="" method="POST" class="col-xs-60 col-sm-35 center-block">
					<input type="hidden" name="ref" id="ref" value="delete_user">
					<input type="hidden" name="user_id" id="user_id" value="<?= $user->id ?>">
					<td><button class="button">Delete</button></td>
				</form>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>


<main id="enquire" style="position: relative;">
</main>
