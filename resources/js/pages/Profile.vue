<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
        <div class="flex items-center space-x-6">
          <!-- Avatar Section -->
          <div class="relative">
            <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border-4 border-white/30">
              <img
                v-if="profile.avatar_url"
                :src="profile.avatar_url"
                :alt="profile.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="text-white text-2xl font-bold">
                {{ getInitials(profile.name) }}
              </div>
            </div>
            <button
              @click="showAvatarModal = true"
              class="absolute -bottom-1 -right-1 bg-white rounded-full p-1.5 shadow-md hover:bg-gray-50 transition-colors"
            >
              <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
            </button>
          </div>
          
          <!-- Profile Info -->
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-white truncate">{{ profile.name }}</h1>
            <p class="text-indigo-100">{{ profile.email }}</p>
            <div class="flex items-center space-x-4 mt-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                {{ profile.role || 'User' }}
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                {{ profile.department?.name || 'No Department' }}
              </span>
              <span class="text-indigo-100 text-sm">
                Member since {{ formatDate(profile.created_at) }}
              </span>
            </div>
          </div>
          
          <!-- Profile Stats -->
          <div class="hidden lg:grid grid-cols-3 gap-6 text-center">
            <div>
              <div class="text-2xl font-bold text-white">{{ stats.documents_created || 0 }}</div>
              <div class="text-indigo-100 text-sm">Documents Created</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-white">{{ stats.documents_processed || 0 }}</div>
              <div class="text-indigo-100 text-sm">Documents Processed</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-white">{{ stats.qr_codes_scanned || 0 }}</div>
              <div class="text-indigo-100 text-sm">QR Codes Scanned</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white shadow rounded-lg">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
          <button
            @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
            Profile Information
          </button>
          <button
            @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd" />
            </svg>
            Security
          </button>
          <button
            @click="activeTab = 'preferences'"
            :class="activeTab === 'preferences' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
            Preferences
          </button>
          <button
            @click="activeTab = 'activity'"
            :class="activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            Recent Activity
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- Profile Information Tab -->
        <div v-show="activeTab === 'profile'" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Basic Information
              </h3>
              
              <form @submit.prevent="updateBasicInfo" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                  <input
                    v-model="profileForm.name"
                    type="text"
                    required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                  <input
                    v-model="profileForm.email"
                    type="email"
                    required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                  <input
                    v-model="profileForm.phone"
                    type="tel"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Position/Title</label>
                  <input
                    v-model="profileForm.position"
                    type="text"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                  <textarea
                    v-model="profileForm.bio"
                    rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Tell us about yourself..."
                  ></textarea>
                </div>
                
                <button
                  type="submit"
                  :disabled="profileLoading"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                >
                  <span v-if="profileLoading">Updating...</span>
                  <span v-else>Update Information</span>
                </button>
              </form>
            </div>
            
            <!-- Account Information -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v6.5a2 2 0 01-2 2h-2v1a3 3 0 01-3 3H8a3 3 0 01-3-3v-1H3a2 2 0 01-2-2V8a2 2 0 012-2h2z" clip-rule="evenodd" />
                </svg>
                Account Information
              </h3>
              
              <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Account ID:</span>
                  <span class="text-sm text-gray-900 font-mono">#{{ profile.id }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Role:</span>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ profile.role || 'User' }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Department:</span>
                  <span class="text-sm text-gray-900">{{ profile.department?.name || 'Not Assigned' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Status:</span>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                      <circle cx="4" cy="4" r="3" />
                    </svg>
                    Active
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Member Since:</span>
                  <span class="text-sm text-gray-900">{{ formatDate(profile.created_at) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Last Login:</span>
                  <span class="text-sm text-gray-900">{{ formatDate(profile.last_login_at) || 'Never' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Security Tab -->
        <div v-show="activeTab === 'security'" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Change Password -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd" />
                </svg>
                Change Password
              </h3>
              
              <form @submit.prevent="updatePassword" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                  <div class="relative">
                    <input
                      v-model="passwordForm.current_password"
                      :type="showCurrentPassword ? 'text' : 'password'"
                      required
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                    />
                    <button
                      type="button"
                      @click="showCurrentPassword = !showCurrentPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg v-if="!showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                      </svg>
                    </button>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                  <div class="relative">
                    <input
                      v-model="passwordForm.password"
                      :type="showNewPassword ? 'text' : 'password'"
                      required
                      minlength="8"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                    />
                    <button
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg v-if="!showNewPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                      </svg>
                    </button>
                  </div>
                  <div class="mt-1 text-xs text-gray-500">
                    Password must be at least 8 characters long
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                  <input
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                
                <button
                  type="submit"
                  :disabled="passwordLoading"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                >
                  <span v-if="passwordLoading">Updating Password...</span>
                  <span v-else>Update Password</span>
                </button>
              </form>
            </div>
            
            <!-- Security Settings -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Security Settings
              </h3>
              
              <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                      <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                    </div>
                    <div class="ml-4">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="securitySettings.two_factor_enabled"
                          @change="updateSecuritySettings"
                          type="checkbox"
                          class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                      </label>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Email Security Alerts</h4>
                      <p class="text-sm text-gray-500">Get notified about security events</p>
                    </div>
                    <div class="ml-4">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="securitySettings.email_alerts"
                          @change="updateSecuritySettings"
                          type="checkbox"
                          class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                      </label>
                    </div>
                  </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-yellow-800">Security Tip</h4>
                      <p class="text-sm text-yellow-700 mt-1">Enable two-factor authentication and keep your password secure to protect your account.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Preferences Tab -->
        <div v-show="activeTab === 'preferences'" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notification Preferences -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
                Notification Preferences
              </h3>
              
              <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                      <p class="text-sm text-gray-500">Receive notifications via email</p>
                    </div>
                    <div class="ml-4">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="preferences.email_notifications"
                          @change="updatePreferences"
                          type="checkbox"
                          class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                      </label>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Document Status Updates</h4>
                      <p class="text-sm text-gray-500">Get notified when document status changes</p>
                    </div>
                    <div class="ml-4">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="preferences.document_notifications"
                          @change="updatePreferences"
                          type="checkbox"
                          class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                      </label>
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Weekly Reports</h4>
                      <p class="text-sm text-gray-500">Receive weekly activity summaries</p>
                    </div>
                    <div class="ml-4">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="preferences.weekly_reports"
                          @change="updatePreferences"
                          type="checkbox"
                          class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Display Preferences -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
                Display Preferences
              </h3>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                  <select
                    v-model="preferences.theme"
                    @change="updatePreferences"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                    <option value="system">System</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                  <select
                    v-model="preferences.language"
                    @change="updatePreferences"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="en">English</option>
                    <option value="fil">Filipino</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                  <select
                    v-model="preferences.date_format"
                    @change="updatePreferences"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                    <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Documents Per Page</label>
                  <select
                    v-model="preferences.documents_per_page"
                    @change="updatePreferences"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Activity Tab -->
        <div v-show="activeTab === 'activity'" class="space-y-6">
          <h3 class="text-lg font-medium text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            Recent Activity
          </h3>
          
          <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div v-if="loadingActivity" class="text-center py-12">
              <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="mt-2 text-sm text-gray-500">Loading activity...</p>
            </div>
            
            <div v-else-if="activities.length === 0" class="text-center py-12">
              <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="mt-2 text-sm text-gray-500">No recent activity</p>
            </div>
            
            <div v-else class="divide-y divide-gray-200">
              <div v-for="activity in activities" :key="activity.id" class="p-4 hover:bg-gray-50">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                      <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-900">{{ activity.description }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(activity.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div v-if="message" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ message }}
      </div>
    </div>
    
    <div v-if="error" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        {{ error }}
      </div>
    </div>
    
    <!-- Avatar Upload Modal -->
    <div v-if="showAvatarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Change Avatar</h3>
          <div class="space-y-4">
            <div class="flex justify-center">
              <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                <img v-if="avatarPreview" :src="avatarPreview" class="w-full h-full object-cover" />
                <img v-else-if="profile.avatar_url" :src="profile.avatar_url" class="w-full h-full object-cover" />
                <div v-else class="text-gray-400 text-xl font-bold">{{ getInitials(profile.name) }}</div>
              </div>
            </div>
            <div>
              <input
                @change="handleAvatarChange"
                type="file"
                accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                @click="showAvatarModal = false"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                @click="uploadAvatar"
                :disabled="!avatarFile || avatarLoading"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                <span v-if="avatarLoading">Uploading...</span>
                <span v-else>Upload</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

const authStore = useAuthStore()

// Main data
const profile = ref({
  id: null,
  name: '',
  email: '',
  avatar_url: '',
  role: '',
  department: null,
  created_at: null,
  last_login_at: null,
  phone: '',
  position: '',
  bio: ''
})

const stats = ref({
  documents_created: 0,
  documents_processed: 0,
  qr_codes_scanned: 0
})

const activities = ref([])

// UI state
const activeTab = ref('profile')
const message = ref('')
const error = ref('')
const loadingActivity = ref(false)

// Form states
const profileLoading = ref(false)
const passwordLoading = ref(false)
const avatarLoading = ref(false)

// Password visibility states
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)

// Avatar upload
const showAvatarModal = ref(false)
const avatarFile = ref(null)
const avatarPreview = ref('')

// Forms
const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  position: '',
  bio: ''
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const securitySettings = reactive({
  two_factor_enabled: false,
  email_alerts: true
})

const preferences = reactive({
  email_notifications: true,
  document_notifications: true,
  weekly_reports: false,
  theme: 'light',
  language: 'en',
  date_format: 'MM/DD/YYYY',
  documents_per_page: 25
})

// Computed
const getInitials = computed(() => {
  return (name) => {
    if (!name) return '??'
    return name.split(' ')
      .map(word => word.charAt(0))
      .join('')
      .toUpperCase()
      .slice(0, 2)
  }
})

// Methods
const fetchProfile = async () => {
  try {
    const id = authStore.user?.id
    if (!id) return
    const response = await axios.get(`/api/users/${id}`)
    const u = response.data?.data || response.data

    profile.value = {
      id: u.id,
      name: u.name || '',
      email: u.email || '',
      avatar_url: u.avatar_url || '',
      role: (u.roles && Array.isArray(u.roles) && u.roles[0]?.name) ? u.roles[0].name : (Array.isArray(u.roles) ? u.roles[0] : 'User'),
      department: u.department || null,
      created_at: u.created_at || null,
      last_login_at: u.last_login_at || null,
      phone: u.phone || '',
      position: u.position || '',
      bio: u.bio || ''
    }

    // Update form values
    profileForm.name = profile.value.name
    profileForm.email = profile.value.email
    profileForm.phone = profile.value.phone
    profileForm.position = profile.value.position
    profileForm.bio = profile.value.bio
  } catch (err) {
    console.error('Error fetching profile:', err)
    showError('Failed to load profile')
  }
}

const fetchStats = async () => {
  try {
    const resp = await axios.get('/api/dashboard/stats')
    const dash = resp.data || {}

    // Header metrics
    stats.value = {
      documents_created: dash.my_documents || 0,
      documents_processed: dash.assigned_to_me || 0,
      qr_codes_scanned: 0
    }

    // Recent activity
    activities.value = Array.isArray(dash.recent_activity) ? dash.recent_activity : []

    // Fetch QR scans performed by current user
    try {
      const scanResp = await axios.get('/api/qr-codes/my-scan-count')
      const count = scanResp.data?.count ?? (scanResp.data || 0)
      stats.value.qr_codes_scanned = count || 0
    } catch (e) {
      // Silently ignore if unavailable; keep zero
    }
  } catch (err) {
    console.error('Error fetching stats:', err)
  }
}

const loadActivity = async () => {
  loadingActivity.value = true
  try {
    const resp = await axios.get('/api/dashboard/stats')
    const dash = resp.data || {}
    activities.value = Array.isArray(dash.recent_activity) ? dash.recent_activity : []
  } catch (err) {
    console.error('Error loading activity:', err)
    activities.value = []
  } finally {
    loadingActivity.value = false
  }
}

const updateBasicInfo = async () => {
  profileLoading.value = true
  clearMessages()
  
  try {
    // Prefer the loaded profile ID, fallback to auth store
    const targetId = profile.value?.id ?? authStore.user?.id
    if (!targetId) {
      showError('Cannot update profile: user ID not loaded')
      profileLoading.value = false
      return
    }

    const response = await axios.put(`/api/users/${targetId}`, {
      name: profileForm.name,
      email: profileForm.email,
      phone: profileForm.phone,
      position: profileForm.position,
      bio: profileForm.bio
    })
    
    // Unwrap ApiResponse and update auth store + local profile
    const updated = response.data?.data || response.data
    authStore.user = {
      ...(authStore.user || {}),
      ...updated
    }
    Object.assign(profile.value, updated)
    
    showSuccess('Profile information updated successfully')
  } catch (err) {
    console.error('Error updating profile:', err)
    showError(err.response?.data?.message || 'Failed to update profile information')
  } finally {
    profileLoading.value = false
  }
}

const updatePassword = async () => {
  if (passwordForm.password !== passwordForm.password_confirmation) {
    showError('New passwords do not match')
    return
  }
  
  passwordLoading.value = true
  clearMessages()
  
  try {
    const targetId = profile.value?.id ?? authStore.user?.id
    if (!targetId) {
      showError('Cannot update password: user ID not loaded')
      passwordLoading.value = false
      return
    }

    await axios.put(`/api/users/${targetId}/password`, {
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation
    })
    
    // Clear form
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
    
    showSuccess('Password updated successfully')
  } catch (err) {
    console.error('Error updating password:', err)
    showError(err.response?.data?.message || 'Failed to update password')
  } finally {
    passwordLoading.value = false
  }
}

const updateSecuritySettings = async () => {
  try {
    // Mock API call for now
    showSuccess('Security settings updated')
  } catch (err) {
    console.error('Error updating security settings:', err)
    showError('Failed to update security settings')
  }
}

const updatePreferences = async () => {
  try {
    // Mock API call for now
    showSuccess('Preferences updated')
  } catch (err) {
    console.error('Error updating preferences:', err)
    showError('Failed to update preferences')
  }
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) { // 2MB limit
      showError('File size must be less than 2MB')
      return
    }
    
    if (!file.type.startsWith('image/')) {
      showError('Please select an image file')
      return
    }
    
    avatarFile.value = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      avatarPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const uploadAvatar = async () => {
  if (!avatarFile.value) return
  
  avatarLoading.value = true
  const formData = new FormData()
  formData.append('avatar', avatarFile.value)
  
  try {
    const targetId = profile.value?.id ?? authStore.user?.id
    if (!targetId) {
      showError('Cannot upload avatar: user ID not loaded')
      avatarLoading.value = false
      return
    }

    const response = await axios.post(`/api/users/${targetId}/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    const payload = response.data?.data || response.data
    const newUrl = payload?.avatar_url || response.data?.avatar_url
    profile.value.avatar_url = newUrl
    if (authStore.user) authStore.user.avatar_url = newUrl
    
    showSuccess('Avatar updated successfully')
    showAvatarModal.value = false
    avatarFile.value = null
    avatarPreview.value = ''
  } catch (err) {
    console.error('Error uploading avatar:', err)
    showError(err.response?.data?.message || 'Failed to upload avatar')
  } finally {
    avatarLoading.value = false
  }
}

const formatDate = (date) => {
  if (!date) return 'Never'
  return new Date(date).toLocaleDateString(preferences.language === 'fil' ? 'fil-PH' : 'en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const showSuccess = (msg) => {
  message.value = msg
  setTimeout(() => {
    message.value = ''
  }, 3000)
}

const showError = (msg) => {
  error.value = msg
  setTimeout(() => {
    error.value = ''
  }, 5000)
}

const clearMessages = () => {
  message.value = ''
  error.value = ''
}

// Lifecycle
onMounted(() => {
  fetchProfile()
  fetchStats()
  loadActivity()
})
</script>
