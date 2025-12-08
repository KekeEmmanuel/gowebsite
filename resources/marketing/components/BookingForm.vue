<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
  packageId: number;
  packageTitle: string;
}

interface CustomizationLocation {
  location: string;
  days: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  success: [];
  cancel: [];
}>();

// Form fields
const form = ref({
  full_name: '',
  email: '',
  phone: '',
  whatsapp: '',
  travel_date: '',
  number_of_travelers: 1,
  customization_data: {
    locations: [] as CustomizationLocation[],
    total_days: 0,
    special_preferences: '',
  } as {
    locations: CustomizationLocation[];
    total_days: number;
    special_preferences: string;
  },
  special_requests: '',
});

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});

// Customization
const newLocation = ref({ location: '', days: 1 });
const showAddLocation = ref(false);

const totalCustomDays = computed(() => {
  return form.value.customization_data.locations.reduce((sum, loc) => sum + loc.days, 0);
});

const addLocation = () => {
  if (newLocation.value.location.trim()) {
    form.value.customization_data.locations.push({
      location: newLocation.value.location,
      days: newLocation.value.days,
    });
    form.value.customization_data.total_days = totalCustomDays.value;
    newLocation.value = { location: '', days: 1 };
    showAddLocation.value = false;
  }
};

const removeLocation = (index: number) => {
  form.value.customization_data.locations.splice(index, 1);
  form.value.customization_data.total_days = totalCustomDays.value;
};

const validate = (): boolean => {
  errors.value = {};

  if (!form.value.full_name.trim()) {
    errors.value.full_name = 'Full name is required';
  }

  if (!form.value.email.trim()) {
    errors.value.email = 'Email is required';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email address';
  }

  if (!form.value.phone.trim()) {
    errors.value.phone = 'Phone number is required';
  }

  if (form.value.travel_date && new Date(form.value.travel_date) < new Date()) {
    errors.value.travel_date = 'Travel date must be in the future';
  }

  if (form.value.number_of_travelers < 1) {
    errors.value.number_of_travelers = 'Number of travelers must be at least 1';
  }

  return Object.keys(errors.value).length === 0;
};

const submit = async () => {
  if (!validate()) {
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        tour_package_id: props.packageId,
        full_name: form.value.full_name,
        email: form.value.email,
        phone: form.value.phone,
        whatsapp: form.value.whatsapp,
        travel_date: form.value.travel_date || null,
        number_of_travelers: form.value.number_of_travelers,
        customization_data: form.value.customization_data,
        special_requests: form.value.special_requests,
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      if (errorData.errors) {
        errors.value = errorData.errors;
      } else {
        throw new Error(errorData.message || 'Failed to submit booking');
      }
      return;
    }

    // Success
    emit('success');
  } catch (error) {
    console.error('Error submitting booking:', error);
    errors.value.submit = 'Failed to submit booking. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="booking-form">
    <h2 class="text-3xl font-heading font-bold text-charcoal mb-2">Book: {{ packageTitle }}</h2>
    <p class="text-charcoal/70 mb-8">Customize your perfect Tanzanian adventure</p>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Personal Information -->
      <div class="space-y-4">
        <h3 class="text-xl font-semibold text-charcoal border-b border-safari-sand/30 pb-2">Personal Information</h3>
        
        <div>
          <label for="full_name" class="block text-sm font-semibold text-charcoal mb-2">
            Full Name <span class="text-red-500">*</span>
          </label>
          <input
            id="full_name"
            v-model="form.full_name"
            type="text"
            required
            class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
            :class="{ 'border-red-500': errors.full_name }"
          />
          <p v-if="errors.full_name" class="mt-1 text-sm text-red-500">{{ errors.full_name }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="email" class="block text-sm font-semibold text-charcoal mb-2">
              Email <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
              :class="{ 'border-red-500': errors.email }"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
          </div>

          <div>
            <label for="phone" class="block text-sm font-semibold text-charcoal mb-2">
              Phone <span class="text-red-500">*</span>
            </label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              required
              class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
              :class="{ 'border-red-500': errors.phone }"
            />
            <p v-if="errors.phone" class="mt-1 text-sm text-red-500">{{ errors.phone }}</p>
          </div>
        </div>

        <div>
          <label for="whatsapp" class="block text-sm font-semibold text-charcoal mb-2">
            WhatsApp (Optional)
          </label>
          <input
            id="whatsapp"
            v-model="form.whatsapp"
            type="tel"
            class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
          />
        </div>
      </div>

      <!-- Travel Details -->
      <div class="space-y-4">
        <h3 class="text-xl font-semibold text-charcoal border-b border-safari-sand/30 pb-2">Travel Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="travel_date" class="block text-sm font-semibold text-charcoal mb-2">
              Preferred Travel Date
            </label>
            <input
              id="travel_date"
              v-model="form.travel_date"
              type="date"
              :min="new Date().toISOString().split('T')[0]"
              class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
              :class="{ 'border-red-500': errors.travel_date }"
            />
            <p v-if="errors.travel_date" class="mt-1 text-sm text-red-500">{{ errors.travel_date }}</p>
          </div>

          <div>
            <label for="number_of_travelers" class="block text-sm font-semibold text-charcoal mb-2">
              Number of Travelers <span class="text-red-500">*</span>
            </label>
            <input
              id="number_of_travelers"
              v-model.number="form.number_of_travelers"
              type="number"
              min="1"
              max="100"
              required
              class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
              :class="{ 'border-red-500': errors.number_of_travelers }"
            />
            <p v-if="errors.number_of_travelers" class="mt-1 text-sm text-red-500">{{ errors.number_of_travelers }}</p>
          </div>
        </div>
      </div>

      <!-- Package Customization -->
      <div class="space-y-4">
        <h3 class="text-xl font-semibold text-charcoal border-b border-safari-sand/30 pb-2">
          Customize Your Package
        </h3>
        <p class="text-sm text-charcoal/70">
          Choose the locations you want to visit and how many days at each location.
        </p>

        <!-- Locations List -->
        <div v-if="form.customization_data.locations.length > 0" class="space-y-3">
          <div
            v-for="(loc, index) in form.customization_data.locations"
            :key="index"
            class="flex items-center gap-3 p-4 rounded-lg border border-safari-sand/30 bg-safari-sand/5"
          >
            <div class="flex-1">
              <p class="font-semibold text-charcoal">{{ loc.location }}</p>
              <p class="text-sm text-charcoal/70">{{ loc.days }} {{ loc.days === 1 ? 'day' : 'days' }}</p>
            </div>
            <button
              type="button"
              @click="removeLocation(index)"
              class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
          <div class="text-sm font-semibold text-charcoal">
            Total Days: <span class="text-safari-gold">{{ totalCustomDays }}</span>
          </div>
        </div>

        <!-- Add Location Form -->
        <div v-if="showAddLocation" class="p-4 rounded-lg border border-safari-gold/30 bg-safari-gold/5">
          <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_auto] gap-3">
            <input
              v-model="newLocation.location"
              type="text"
              placeholder="Location name (e.g., Serengeti, Zanzibar)"
              class="rounded-lg border border-safari-sand/30 bg-white px-4 py-2 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
            />
            <input
              v-model.number="newLocation.days"
              type="number"
              min="1"
              max="30"
              placeholder="Days"
              class="rounded-lg border border-safari-sand/30 bg-white px-4 py-2 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20 w-24"
            />
            <div class="flex gap-2">
              <button
                type="button"
                @click="addLocation"
                class="px-4 py-2 bg-safari-gold text-charcoal rounded-lg font-semibold hover:bg-safari-gold-light transition"
              >
                Add
              </button>
              <button
                type="button"
                @click="showAddLocation = false; newLocation = { location: '', days: 1 }"
                class="px-4 py-2 bg-charcoal/10 text-charcoal rounded-lg font-semibold hover:bg-charcoal/20 transition"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>

        <!-- Add Location Button -->
        <button
          v-else
          type="button"
          @click="showAddLocation = true"
          class="w-full py-3 border-2 border-dashed border-safari-gold/50 rounded-lg text-safari-gold font-semibold hover:border-safari-gold hover:bg-safari-gold/5 transition"
        >
          + Add Location
        </button>

        <!-- Special Preferences -->
        <div>
          <label for="special_preferences" class="block text-sm font-semibold text-charcoal mb-2">
            Special Preferences (Optional)
          </label>
          <textarea
            id="special_preferences"
            v-model="form.customization_data.special_preferences"
            rows="3"
            placeholder="Any special preferences for accommodations, activities, etc."
            class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
          ></textarea>
        </div>
      </div>

      <!-- Special Requests -->
      <div>
        <label for="special_requests" class="block text-sm font-semibold text-charcoal mb-2">
          Additional Requests or Notes
        </label>
        <textarea
          id="special_requests"
          v-model="form.special_requests"
          rows="4"
          placeholder="Any additional information, special requests, or questions..."
          class="w-full rounded-lg border border-safari-sand/30 bg-white px-4 py-3 text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
        ></textarea>
      </div>

      <!-- Error Message -->
      <div v-if="errors.submit" class="p-4 rounded-lg bg-red-50 border border-red-200">
        <p class="text-sm text-red-600">{{ errors.submit }}</p>
      </div>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-4 pt-4">
        <button
          type="submit"
          :disabled="isSubmitting"
          class="flex-1 rounded-full bg-safari-gold px-8 py-4 text-sm font-semibold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="isSubmitting" class="inline-flex items-center gap-2">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting...
          </span>
          <span v-else>Submit Booking Request</span>
        </button>
        <button
          type="button"
          @click="emit('cancel')"
          class="px-8 py-4 rounded-full border-2 border-safari-sand/30 bg-white text-charcoal font-semibold transition hover:border-safari-gold hover:bg-safari-sand/5"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

