<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">カテゴリー管理</h1>
      <button
        @click="openCreateModal"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        新規作成
      </button>
    </div>

    <!-- カテゴリー一覧 -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              名前
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              親カテゴリー
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              操作
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">
                {{ getParentCategoryName(category.parent_id) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="openEditModal(category)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                編集
              </button>
              <button
                @click="confirmDelete(category)"
                class="text-red-600 hover:text-red-900"
              >
                削除
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 作成モーダル -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">カテゴリー作成</h3>
          <form @submit.prevent="createCategory">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                名前
              </label>
              <input
                v-model="form.name"
                type="text"
                id="name"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              >
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="parent_id">
                親カテゴリー
              </label>
              <select
                v-model="form.parent_id"
                id="parent_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              >
                <option :value="null">なし</option>
                <option v-for="category in rootCategories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div class="flex justify-end">
              <button
                type="button"
                @click="closeCreateModal"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2"
              >
                キャンセル
              </button>
              <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                作成
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- 編集モーダル -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">カテゴリー編集</h3>
          <form @submit.prevent="updateCategory">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-name">
                名前
              </label>
              <input
                v-model="editForm.name"
                type="text"
                id="edit-name"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              >
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-parent_id">
                親カテゴリー
              </label>
              <select
                v-model="editForm.parent_id"
                id="edit-parent_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              >
                <option :value="null">なし</option>
                <option v-for="category in rootCategories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <div class="flex justify-end">
              <button
                type="button"
                @click="closeEditModal"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2"
              >
                キャンセル
              </button>
              <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                更新
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- 削除確認モーダル -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">削除の確認</h3>
          <p class="text-sm text-gray-500 mb-4">
            「{{ categoryToDelete?.name }}」を削除してもよろしいですか？
          </p>
          <div class="flex justify-end">
            <button
              @click="closeDeleteModal"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2"
            >
              キャンセル
            </button>
            <button
              @click="deleteCategory"
              class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
            >
              削除
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const categories = ref([])
const rootCategories = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const categoryToDelete = ref(null)

const form = ref({
  name: '',
  parent_id: null
})

const editForm = ref({
  id: null,
  name: '',
  parent_id: null
})

// カテゴリー一覧を取得
const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories')
    categories.value = response.data
  } catch (error) {
    console.error('カテゴリーの取得に失敗しました:', error)
  }
}

// 親カテゴリー一覧を取得
const fetchRootCategories = async () => {
  try {
    const response = await axios.get('/api/categories/create')
    rootCategories.value = response.data.rootCategories
  } catch (error) {
    console.error('親カテゴリーの取得に失敗しました:', error)
  }
}

// 親カテゴリー名を取得
const getParentCategoryName = (parentId) => {
  if (!parentId) return 'なし'
  const parent = categories.value.find(c => c.id === parentId)
  return parent ? parent.name : '不明'
}

// 作成モーダルを開く
const openCreateModal = () => {
  form.value = {
    name: '',
    parent_id: null
  }
  showCreateModal.value = true
}

// 作成モーダルを閉じる
const closeCreateModal = () => {
  showCreateModal.value = false
}

// 編集モーダルを開く
const openEditModal = (category) => {
  editForm.value = {
    id: category.id,
    name: category.name,
    parent_id: category.parent_id
  }
  showEditModal.value = true
}

// 編集モーダルを閉じる
const closeEditModal = () => {
  showEditModal.value = false
}

// 削除確認モーダルを開く
const confirmDelete = (category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

// 削除確認モーダルを閉じる
const closeDeleteModal = () => {
  showDeleteModal.value = false
  categoryToDelete.value = null
}

// カテゴリーを作成
const createCategory = async () => {
  try {
    await axios.post('/api/categories', form.value)
    await fetchCategories()
    await fetchRootCategories()
    closeCreateModal()
  } catch (error) {
    console.error('カテゴリーの作成に失敗しました:', error)
  }
}

// カテゴリーを更新
const updateCategory = async () => {
  try {
    await axios.put(`/api/categories/${editForm.value.id}`, editForm.value)
    await fetchCategories()
    await fetchRootCategories()
    closeEditModal()
  } catch (error) {
    console.error('カテゴリーの更新に失敗しました:', error)
  }
}

// カテゴリーを削除
const deleteCategory = async () => {
  try {
    await axios.delete(`/api/categories/${categoryToDelete.value.id}`)
    await fetchCategories()
    await fetchRootCategories()
    closeDeleteModal()
  } catch (error) {
    console.error('カテゴリーの削除に失敗しました:', error)
  }
}

onMounted(() => {
  fetchCategories()
  fetchRootCategories()
})
</script> 