@extends('layout')
@section('title', 'Home')
@section('content')
<div class="container my-4">
   <h1 class="text-center mb-4">User Form</h1>
   <!-- Success Message -->
   <div id="successMessage" class="alert alert-success d-none" role="alert">
	  User added successfully!
   </div>
   <!-- Form -->
   <form id="userForm" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
	  @csrf
	  <div class="row">
		 <!-- Name -->
		 <div class="col-md-6 position-relative mb-3">
			<label for="name" class="form-label">Name <span style="color: red;">*</span></label>
			<input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required />
			<div class="invalid-feedback">Please enter your name.</div>
			<div class="error-message" id="nameError" style="color: red;"></div>
		 </div>
		 <!-- Email -->
		 <div class="col-md-6 position-relative mb-3">
			<label for="email" class="form-label">Email <span style="color: red;">*</span></label>
			<input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required />
			<div class="invalid-feedback">Please enter a valid email.</div>
			<div class="error-message" id="emailError" style="color: red;"></div>
		 </div>
		 <!-- Phone -->
		 <div class="col-md-6 position-relative mb-3">
			<label for="phone" class="form-label">Phone <span style="color: red;">*</span></label>
			<input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required />
			<div class="invalid-feedback">Please enter a valid phone number.</div>
			<div class="error-message" id="phoneError" style="color: red;"></div>
		 </div>
		 <!-- Description -->
		 <div class="col-md-6 position-relative mb-3">
			<label for="description" class="form-label">Description</label>
			<textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
			<div class="error-message" id="descriptionError" style="color: red;"></div>
		 </div>
		 <!-- Role -->
		 <div class="col-md-6 position-relative mb-3">
			<label for="role_id" class="form-label">Role</label>
			<select id="role_id" name="role_id" class="form-select" required>
			   <option value="" disabled selected>Select Role</option>
			   @foreach ($roles as $role)
			   <option value="{{ $role->id }}">{{ $role->name }}</option>
			   @endforeach
			</select>
			<div class="invalid-feedback">Please select a role.</div>
			<div class="error-message" id="roleError" style="color: red;"></div>
		 </div>
		 <!-- Profile Image -->
		 <div class="col-md-6 mb-3">
			<label for="profile_image" class="form-label">Profile Image</label>
			<input type="file" id="profile_image" name="profile_image" class="form-control" onchange="previewImage()" />
			<div class="image-preview" id="imagePreview"></div>
			<button type="button" id="removeImage" class="btn btn-danger mt-2 d-none" onclick="removeProfile()">Remove Image</button>
		 </div>
	  </div>
	  <div class="row mt-4 mb-3">
		 <div class="col-12 text-center">
			<button type="submit" class="btn btn-primary">
			<span class="spinner-border spinner-border-sm d-none" role="status" id="submitLoader"></span>
			Submit
			</button>
		 </div>
	  </div>
   </form>
   <!-- Live Search Input -->
   <div class="row mb-3">
	  <div class="col-12">
		 <input type="text" id="searchInput" class="form-control" placeholder="Search by name,email or phone..." oninput="liveSearch()" />
	  </div>
   </div>
   <!-- Table for displaying users -->
   <table id="userTable" class="table">
	  <thead>
		 <tr>
			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Description</th>
			<th>Role</th>
			<th>Profile Image</th>
		 </tr>
	  </thead>
	  <tbody>
		 <!-- User data will be injected here -->
	  </tbody>
   </table>
   <!-- Loader -->
   <div id="tableLoader" class="d-none">Loading...</div>
   <!-- Pagination controls -->
   <div id="paginationControls" class="pagination-container">
	  <!-- Pagination buttons will be injected here -->
   </div>
</div>
@endsection
@push('scripts')
<script>
   // form submission
   document.getElementById('userForm').addEventListener('submit', function (e) {
	   e.preventDefault();
   
	   const submitLoader = document.getElementById('submitLoader');
	   submitLoader.classList.remove('d-none');
	   clearErrorMessages();
   
	   let formData = new FormData(this);
	   let isValid = true;
   
	   if (!isValid) {
		   submitLoader.classList.add('d-none');
		   return;
	   }
   
	   fetch('/api/users', {
		   method: 'POST',
		   body: formData,
	   })
		   .then(response => response.json())
		   .then(data => {
			   submitLoader.classList.add('d-none');
			   if (data.errors) {
				   for (const [field, messages] of Object.entries(data.errors)) {
					   const errorField = document.getElementById(`${field}Error`);
					   errorField.innerText = messages.join(', ');
				   }
			   } else {
				   document.getElementById('successMessage').classList.remove('d-none');
				   clearForm();
				   fetchUsers();  // Fetch updated users after success
			   }
		   })
		   .catch(error => {
			   submitLoader.classList.add('d-none');
			   console.error('Error:', error);
		   });
   });
   
   // Function to clear form fields
   function clearForm() {
	   document.getElementById('userForm').reset();
	   document.getElementById('imagePreview').innerHTML = '';
	   document.getElementById('removeImage').classList.add('d-none');
   }
   
   // Global variable to hold current page
   let currentPage = 1;
   
   function getPaginationControls(data) {
	   let paginationHTML = '';
	   const searchTerm = document.getElementById('searchInput').value.toLowerCase();
   
	   // Previous page button
	   if (data.current_page > 1) {
		   paginationHTML += `<button class="pagination-btn" onclick="fetchUsers(${data.current_page - 1}, '${searchTerm}')">Prev</button>`;
	   }
   
	   // Next page button
	   if (data.current_page < data.last_page) {
		   paginationHTML += `<button class="pagination-btn" onclick="fetchUsers(${data.current_page + 1}, '${searchTerm}')">Next</button>`;
	   }
   
	   return paginationHTML;
   }
   
   // Update fetch function to handle the search term with pagination
   function fetchUsers(page = 1, searchTerm = '') {
	   const tableLoader = document.getElementById('tableLoader');
	   const userTable = document.getElementById('userTable');
	   const paginationControls = document.getElementById('paginationControls');
   
	   tableLoader.classList.remove('d-none');
	   userTable.classList.add('d-none');
   
	   fetch(`/api/users?page=${page}&search=${searchTerm}`)
		   .then(response => response.json())
		   .then(data => {
			   if (Array.isArray(data.data)) {
				   const tableBody = document.querySelector('#userTable tbody');
				   tableBody.innerHTML = '';
   
				   data.data.forEach(user => {
					   const row = `<tr>
						   <td>${user.name}</td>
						   <td>${user.email}</td>
						   <td>${user.phone}</td>
						   <td>${user.description}</td>
						   <td>${user.role.name}</td>
						   <td><img src="/storage/${user.profile_image}" width="50" /></td>
					   </tr>`;
					   tableBody.innerHTML += row;
				   });
   
				   tableLoader.classList.add('d-none');
				   userTable.classList.remove('d-none');
				   paginationControls.innerHTML = getPaginationControls(data);
			   } else {
				   console.error('Error: Expected an array of users but received', data);
				   tableLoader.classList.add('d-none');
				   alert('Failed to fetch user data.');
			   }
		   })
		   .catch(error => {
			   tableLoader.classList.add('d-none');
			   console.error('Error fetching users:', error);
		   });
   }
   
   
   // Initial fetch call
   fetchUsers();
   
   // Live search function
   function liveSearch() {
	   const searchTerm = document.getElementById('searchInput').value.toLowerCase();
	   const tableLoader = document.getElementById('tableLoader');
	   const userTable = document.getElementById('userTable');
	   const paginationControls = document.getElementById('paginationControls');
   
	   tableLoader.classList.remove('d-none');
	   userTable.classList.add('d-none');
   
	   fetch(`/api/users?search=${searchTerm}`)
		   .then(response => response.json())
		   .then(data => {
			   if (Array.isArray(data.data)) {
				   const tableBody = document.querySelector('#userTable tbody');
				   tableBody.innerHTML = '';
   
				   // Append each user to the table
				   data.data.forEach(user => {
					   const row = `<tr>
						   <td>${user.name}</td>
						   <td>${user.email}</td>
						   <td>${user.phone}</td>
						   <td>${user.description}</td>
						   <td>${user.role.name}</td>
						   <td><img src="/storage/${user.profile_image}" width="50" /></td>
					   </tr>`;
					   tableBody.innerHTML += row;
				   });
   
				   tableLoader.classList.add('d-none');
				   userTable.classList.remove('d-none');
				   paginationControls.innerHTML = getPaginationControls(data);
			   } else {
				   console.error('Error: Expected an array of users but received', data);
				   tableLoader.classList.add('d-none');
				   alert('Failed to fetch user data.');
			   }
		   })
		   .catch(error => {
			   tableLoader.classList.add('d-none');
			   console.error('Error fetching users:', error);
		   });
   }
   
   
   
   // Clear error messages
   function clearErrorMessages() {
	   document.querySelectorAll('.error-message').forEach(msg => msg.innerHTML = '');
   }
</script>
@endpush