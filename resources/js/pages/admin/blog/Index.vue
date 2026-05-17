<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-2">
      <h1 class="text-3xl font-bold text-gray-900">Blog Management</h1>
      <button @click="openCreatePostModal" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded transition duration-150 shadow-md">
        Create New Post
      </button>
    </div>

    <!-- Blog Categories Section (Placeholder for management UI) -->
    <div class="mb-8 p-6 bg-gray-50 border rounded-lg">
      <h2 class="text-xl font-semibold mb-4 text-gray-700">Blog Categories</h2>
      <p class="text-gray-500 mb-4">Manage blog categories here. (Placeholder)</p>
      <!-- Category list/management component will go here -->
    </div>

    <!-- Blog Posts List Table -->
    <div>
      <h2 class="text-xl font-semibold mb-4 text-gray-700">Published Articles</h2>
      <div v-if="posts.length === 0" class="p-6 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
        No blog posts found. Start by creating a new post!
      </div>

      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-hidden border-b">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Category</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Example Post Row -->
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 cursor-pointer hover:underline">
                  Understanding Modern Web Architecture
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">Development</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <button @click="editPost('post1')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                  <button @click="deletePost('post1')" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination placeholder -->
      <div class="mt-8 flex justify-center">
        <a href="#" class="px-4 py-2 mx-1 border rounded bg-white hover:bg-gray-50">Previous</a>
        <a href="#" class="px-4 py-2 mx-1 border rounded bg-indigo-600 text-white">1</a>
        <a href="#" class="px-4 py-2 mx-1 border rounded bg-white hover:bg-gray-50">2</a>
        <a href="#" class="px-4 py-2 mx-1 border rounded bg-white hover:bg-gray-50">Next</a>
      </div>
    </div>

    <!-- Modal for Creating/Editing Posts (Simplified) -->
    <div v-show="isCreateModalOpen" class="fixed inset-0 overflow-y-auto z-50">
      <div class="flex justify-center items-start h-full pt-4 px-4 pb-12 text-center sm:block sm:p-0">
        <!-- Modal Panel -->
        <div class="inline-block align-relative max-w-7xl max-h-full scaled-down sm:my-8 sm:align-middle sm:rounded-lg sm:overflow-hidden bg-white border shadow-2xl">
          <div class="p-6 md:p-10">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Create New Blog Post</h3>
            <!-- Form fields here -->
            <form @submit.prevent="createPost">
              <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" v-model="postData.title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
              </div>

              <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="category" v-model="postData.categoryId" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                  <option value="" disabled>Select a category</option>
                  <!-- Categories populated via API -->
                </select>
              </div>

              <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700">Content (HTML)</label>
                <textarea id="content" v-model="postData.content" rows="8" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
              </div>

              <div class="flex justify-end space-x-4">
                <button type="button" @click="closeCreatePostModal" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700">Publish Post</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isCreateModalOpen = ref(false);
const postData = ref({
  title: '',
  categoryId: null,
  content: ''
});
const posts = ref([]); // Mock data for the table listing

// Mock methods (will require API integration later)
function openCreatePostModal() {
  isCreateModalOpen.value = true;
}
function closeCreatePostModal() {
  isCreateModalOpen.value = false;
}
function createPost() {
  console.log('Creating post with:', postData.value);
  // API call to save data...
  alert('Post created successfully (Mock)');
  closeCreatePostModal();
}
function editPost(postId) {
  alert(`Editing post ${postId} (Mock)`);
}
function deletePost(postId) {
  if (confirm(`Are you sure you want to delete ${postId}?`)) {
    console.log(`Deleting post ${postId}`);
  }
}

onMounted(() => {
  // Fetch initial list of posts and categories upon component mount
  fetchPostsAndCategories();
});

async function fetchPostsAndCategories() {
  // Simulate fetching data
  await new Promise(resolve => setTimeout(resolve, 500));

  posts.value = [
    { id: 'post1', title: 'Understanding Modern Web Architecture', category: 'Development' },
    { id: 'post2', title: 'Best Practices in Laravel API Design', category: 'Tutorials' }
  ];
}
</script>

<style scoped>
/* Add any specific module styles here */
</style>