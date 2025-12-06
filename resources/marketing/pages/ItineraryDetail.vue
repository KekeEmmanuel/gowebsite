<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

type GalleryImage = {
  id: number;
  url: string;
  thumb: string;
  cover: string;
  name: string;
  alt?: string;
};

type Itinerary = {
  id: number;
  slug: string;
  title: string;
  summary: string;
  badge: string;
  image: string;
  meta: string;
  duration_days: number | null;
  price_from: number | string | null;
  difficulty: string | null;
  highlights: string[];
  inclusions: string[] | null;
  exclusions: string[] | null;
  tags: string[] | null;
  service_type: { name: string; slug: string } | null;
  destination: { name: string; slug: string; region: string } | null;
  gallery?: GalleryImage[];
};

const route = useRoute();
const router = useRouter();
const itinerary = ref<Itinerary | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);
const selectedImageIndex = ref(0);
const showImageModal = ref(false);

// Booking form state
const showBookingForm = ref(false);
const isSubmittingBooking = ref(false);
const bookingSuccess = ref(false);
const bookingError = ref<string | null>(null);
const bookingForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  country: '',
  number_of_travellers: 1,
  preferred_travel_date: '',
  special_requests: '',
});

onMounted(async () => {
  const slug = route.params.slug as string;
  
  try {
    const response = await fetch(`/api/itineraries/${slug}`);
    if (!response.ok) {
      if (response.status === 404) {
        error.value = 'Itinerary not found';
      } else {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return;
    }
    
    const data = await response.json();
    itinerary.value = data;
  } catch (err) {
    console.error('Error fetching itinerary:', err);
    error.value = 'Failed to load itinerary details';
  } finally {
    isLoading.value = false;
  }
});

// Booking form handlers
const submitBooking = async () => {
  if (!itinerary.value) return;
  
  bookingError.value = null;
  isSubmittingBooking.value = true;
  
  try {
    const response = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        itinerary_id: itinerary.value.id,
        ...bookingForm.value,
      }),
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to submit booking');
    }
    
    bookingSuccess.value = true;
    // Reset form
    bookingForm.value = {
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      country: '',
      number_of_travellers: 1,
      preferred_travel_date: '',
      special_requests: '',
    };
    
    // Hide success message after 5 seconds
    setTimeout(() => {
      bookingSuccess.value = false;
      showBookingForm.value = false;
    }, 5000);
  } catch (err: any) {
    console.error('Booking error:', err);
    bookingError.value = err.message || 'An error occurred. Please try again.';
  } finally {
    isSubmittingBooking.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-white text-charcoal">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-black/85 backdrop-blur">
      <div class="mx-auto grid w-full max-w-7xl grid-cols-[minmax(240px,500px)_1fr_auto] items-center gap-4 px-4 py-2.5 lg:gap-8 lg:px-8">
        <router-link to="/" class="flex items-center justify-start">
          <img
            src="/images/safari/mpya.png"
            alt="Go Tanzania Safari"
            class="h-[6.5rem] w-full max-w-[500px] object-contain lg:h-[7rem]"
            loading="lazy"
          />
        </router-link>
        <div class="hidden items-center justify-center lg:flex">
          <nav class="flex items-center gap-4 rounded-full border border-white/10 bg-white/5 px-5 py-2 text-[11px] font-semibold uppercase tracking-[0.36em] text-white/70">
            <router-link to="/#home" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Home</router-link>
            <span class="h-3.5 w-px bg-white/15"></span>
            <router-link to="/#about" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">About</router-link>
            <span class="h-3.5 w-px bg-white/15"></span>
            <router-link to="/#safaris" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Safaris</router-link>
            <span class="h-3.5 w-px bg-white/15"></span>
            <router-link to="/#destinations" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Destinations</router-link>
            <span class="h-3.5 w-px bg-white/15"></span>
            <router-link to="/#lodges" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Lodges</router-link>
            <span class="h-3.5 w-px bg-white/15"></span>
            <router-link to="/#contact" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Contact</router-link>
          </nav>
        </div>
        <div class="flex items-center justify-end gap-3">
          <router-link
            to="/#contact"
            class="hidden rounded-full border border-safari-gold px-5 py-1.5 text-[11px] font-semibold uppercase tracking-[0.36em] text-safari-gold transition hover:bg-safari-gold hover:text-charcoal lg:inline-flex"
          >
            Start Planning
          </router-link>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="isLoading" class="relative flex min-h-screen items-center justify-center overflow-hidden bg-safari-sand/20">
      <!-- Faded background image -->
      <div class="absolute inset-0 opacity-[0.15]">
        <img
          src="/images/safari/wildlife-herd.jpg"
          alt=""
          class="h-full w-full object-cover object-center"
          loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-white/30 via-white/15 to-white/30"></div>
      </div>
      <div class="relative z-10 text-center">
        <div class="mb-4 inline-block h-12 w-12 animate-spin rounded-full border-4 border-safari-gold border-t-transparent"></div>
        <p class="text-lg font-semibold text-charcoal">Loading itinerary details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="relative flex min-h-screen items-center justify-center overflow-hidden bg-safari-sand/20 px-6">
      <!-- Faded background image -->
      <div class="absolute inset-0 opacity-[0.15]">
        <img
          src="/images/safari/wildlife-giraffe.jpg"
          alt=""
          class="h-full w-full object-cover object-center"
          loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-white/30 via-white/15 to-white/30"></div>
      </div>
      <div class="relative z-10 max-w-md text-center">
        <h1 class="text-3xl font-heading text-charcoal mb-4">Itinerary Not Found</h1>
        <p class="text-charcoal/70 mb-6">{{ error }}</p>
        <router-link
          to="/#safaris"
          class="inline-flex items-center gap-2 rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Safaris
        </router-link>
      </div>
    </div>

    <!-- Itinerary Detail Content -->
    <div v-else-if="itinerary" class="relative bg-white">
      <!-- Faded background image for main content -->
      <div class="absolute inset-0 opacity-[0.15] pointer-events-none">
        <img
          src="/images/safari/wildlife-zebra.jpg"
          alt=""
          class="h-full w-full object-cover object-center"
          loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-white/30 via-white/15 to-white/30"></div>
      </div>
      
      <!-- Hero Image Section -->
      <div class="relative z-20 h-[60vh] min-h-[500px] overflow-hidden">
        <img
          :src="itinerary.image"
          :alt="itinerary.title"
          class="h-full w-full object-cover"
          loading="eager"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-black/40"></div>
        
        <div class="absolute inset-0 flex items-end">
          <div class="relative z-10 mx-auto w-full max-w-7xl px-6 pb-16">
            <div class="flex items-center gap-4 mb-4">
              <span class="inline-flex items-center rounded-full bg-safari-gold/95 px-5 py-2 text-xs font-bold uppercase tracking-wider text-charcoal shadow-lg">
                {{ itinerary.badge || 'Signature Collection' }}
              </span>
              <button
                @click="router.back()"
                class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-black/40 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-black/60"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
              </button>
            </div>
            <h1 class="text-5xl font-heading text-white mb-4 sm:text-6xl lg:text-7xl" v-html="itinerary.title"></h1>
            <div class="flex flex-wrap items-center gap-6 text-white/90">
              <span v-if="itinerary.duration_days" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ itinerary.duration_days }} days
              </span>
              <span v-if="itinerary.difficulty" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                {{ itinerary.difficulty }}
              </span>
              <span v-if="itinerary.destination" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ itinerary.destination.name }}
              </span>
              <span v-if="itinerary.service_type" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                {{ itinerary.service_type.name }}
              </span>
            </div>
            <p v-if="itinerary.meta" class="mt-4 text-lg font-semibold text-safari-gold" v-html="itinerary.meta"></p>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="relative z-10 mx-auto max-w-5xl px-6 py-16">
        <div class="grid gap-12 lg:grid-cols-[2fr_1fr]">
          <!-- Main Content Column -->
          <div class="space-y-12">
            <!-- Summary -->
            <section>
              <h2 class="text-3xl font-heading text-charcoal mb-4">Overview</h2>
              <p v-if="itinerary.summary" class="text-lg leading-relaxed text-charcoal/80" v-html="itinerary.summary"></p>
            </section>

            <!-- Gallery -->
            <section v-if="itinerary.gallery && itinerary.gallery.length > 0">
              <h2 class="text-3xl font-heading text-charcoal mb-6">Photo Gallery</h2>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="(image, index) in itinerary.gallery"
                  :key="image.id"
                  @click="selectedImageIndex = index; showImageModal = true"
                  class="group relative aspect-[4/3] cursor-pointer overflow-hidden rounded-2xl bg-safari-sand/20 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                >
                  <img
                    :src="image.thumb || image.url"
                    :alt="image.alt || image.name || `Gallery image ${index + 1}`"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                  <div class="absolute bottom-0 left-0 right-0 p-4 text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <p class="text-sm font-semibold">{{ image.name || `Image ${index + 1}` }}</p>
                  </div>
                </div>
              </div>
            </section>

            <!-- Highlights -->
            <section v-if="itinerary.highlights && itinerary.highlights.length > 0">
              <h2 class="text-3xl font-heading text-charcoal mb-6">Highlights</h2>
              <ul class="space-y-4">
                <li v-for="(highlight, index) in itinerary.highlights" :key="index" class="flex gap-4">
                  <span class="mt-2 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-safari-gold text-sm font-bold text-charcoal">
                    {{ index + 1 }}
                  </span>
                  <p class="flex-1 text-base leading-relaxed text-charcoal/80" v-html="highlight"></p>
                </li>
              </ul>
            </section>

            <!-- Inclusions -->
            <section v-if="itinerary.inclusions && itinerary.inclusions.length > 0">
              <h2 class="text-3xl font-heading text-charcoal mb-6">What's Included</h2>
              <ul class="space-y-3">
                <li v-for="(inclusion, index) in itinerary.inclusions" :key="index" class="flex items-start gap-3">
                  <svg class="mt-1 h-5 w-5 flex-shrink-0 text-safari-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="text-base text-charcoal/80" v-html="inclusion"></span>
                </li>
              </ul>
            </section>

            <!-- Exclusions -->
            <section v-if="itinerary.exclusions && itinerary.exclusions.length > 0">
              <h2 class="text-3xl font-heading text-charcoal mb-6">What's Not Included</h2>
              <ul class="space-y-3">
                <li v-for="(exclusion, index) in itinerary.exclusions" :key="index" class="flex items-start gap-3">
                  <svg class="mt-1 h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                  <span class="text-base text-charcoal/80" v-html="exclusion"></span>
                </li>
              </ul>
            </section>

            <!-- Tags -->
            <section v-if="itinerary.tags && itinerary.tags.length > 0">
              <h2 class="text-3xl font-heading text-charcoal mb-6">Tags</h2>
              <div class="flex flex-wrap gap-3">
                <span
                  v-for="tag in itinerary.tags"
                  :key="tag"
                  class="rounded-full border border-safari-sand bg-safari-sand/30 px-4 py-2 text-sm font-semibold text-charcoal/70"
                >
                  {{ tag }}
                </span>
              </div>
            </section>
          </div>

          <!-- Sidebar -->
          <aside class="lg:sticky lg:top-24 lg:h-fit">
            <div class="rounded-3xl border border-safari-sand/60 bg-safari-sand/20 p-8">
              <!-- Price -->
              <div v-if="itinerary.price_from" class="mb-8 pb-8 border-b border-safari-sand/60">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-charcoal/60 mb-2">Starting from</p>
                <p class="text-4xl font-bold text-safari-gold">
                  ${{ typeof itinerary.price_from === 'number' ? itinerary.price_from.toLocaleString() : itinerary.price_from }}
                </p>
                <p class="mt-2 text-sm text-charcoal/60">per person</p>
              </div>

              <!-- Quick Info -->
              <div class="space-y-6 mb-8">
                <div v-if="itinerary.duration_days">
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-charcoal/60 mb-1">Duration</p>
                  <p class="text-lg font-semibold text-charcoal">{{ itinerary.duration_days }} days</p>
                </div>
                <div v-if="itinerary.difficulty">
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-charcoal/60 mb-1">Difficulty</p>
                  <p class="text-lg font-semibold text-charcoal">{{ itinerary.difficulty }}</p>
                </div>
                <div v-if="itinerary.destination">
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-charcoal/60 mb-1">Destination</p>
                  <p class="text-lg font-semibold text-charcoal">{{ itinerary.destination.name }}</p>
                  <p v-if="itinerary.destination.region" class="text-sm text-charcoal/60">{{ itinerary.destination.region }}</p>
                </div>
              </div>

              <!-- Booking Form -->
              <div class="space-y-4">
                <button
                  v-if="!showBookingForm"
                  @click="showBookingForm = true"
                  class="block w-full rounded-full bg-safari-gold px-6 py-4 text-center text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90 hover:shadow-lg"
                >
                  Book This Safari
                </button>
                
                <!-- Booking Form -->
                <div v-if="showBookingForm" class="space-y-4">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading text-charcoal">Book This Safari</h3>
                    <button
                      @click="showBookingForm = false"
                      class="text-charcoal/60 hover:text-charcoal"
                      aria-label="Close booking form"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- Success Message -->
                  <div v-if="bookingSuccess" class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    <p class="font-semibold mb-1">Booking Request Submitted!</p>
                    <p>We'll contact you within 24 hours to confirm your booking.</p>
                  </div>
                  
                  <!-- Error Message -->
                  <div v-if="bookingError" class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                    {{ bookingError }}
                  </div>
                  
                  <form @submit.prevent="submitBooking" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">First Name *</label>
                        <input
                          v-model="bookingForm.first_name"
                          type="text"
                          required
                          class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                        />
                      </div>
                      <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Last Name *</label>
                        <input
                          v-model="bookingForm.last_name"
                          type="text"
                          required
                          class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                        />
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Email *</label>
                      <input
                        v-model="bookingForm.email"
                        type="email"
                        required
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Phone *</label>
                      <input
                        v-model="bookingForm.phone"
                        type="tel"
                        required
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Country</label>
                      <input
                        v-model="bookingForm.country"
                        type="text"
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Number of Travellers *</label>
                      <input
                        v-model.number="bookingForm.number_of_travellers"
                        type="number"
                        min="1"
                        max="50"
                        required
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Preferred Travel Date</label>
                      <input
                        v-model="bookingForm.preferred_travel_date"
                        type="date"
                        :min="new Date().toISOString().split('T')[0]"
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70 mb-1.5">Special Requests</label>
                      <textarea
                        v-model="bookingForm.special_requests"
                        rows="3"
                        class="w-full rounded-lg border border-safari-sand/60 bg-white px-4 py-2.5 text-sm text-charcoal focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/20"
                        placeholder="Any special requirements or questions..."
                      ></textarea>
                    </div>
                    
                    <button
                      type="submit"
                      :disabled="isSubmittingBooking"
                      class="w-full rounded-full bg-safari-gold px-6 py-4 text-center text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span v-if="isSubmittingBooking">Submitting...</span>
                      <span v-else>Submit Booking Request</span>
                    </button>
                  </form>
                </div>
                
                <a
                  v-if="!showBookingForm"
                  href="#contact"
                  class="block w-full rounded-full border-2 border-safari-green px-6 py-4 text-center text-sm font-semibold text-safari-green transition hover:bg-safari-green hover:text-white"
                >
                  Tailor This Itinerary
                </a>
              </div>

              <!-- Contact Info -->
              <div class="mt-8 rounded-2xl border border-safari-sand/60 bg-white/50 p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold mb-3">Need Help?</p>
                <p class="text-sm text-charcoal/70 mb-4">Our travel experts are here to answer your questions.</p>
                <div class="space-y-2 text-sm">
                  <a href="tel:+255742123456" class="flex items-center gap-2 text-charcoal/80 hover:text-safari-gold">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    +255 (0) 742 123 456
                  </a>
                  <a href="mailto:bookings@gotanzaniasafari.com" class="flex items-center gap-2 text-charcoal/80 hover:text-safari-gold">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    bookings@gotanzaniasafari.com
                  </a>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div
      v-if="showImageModal && itinerary?.gallery && itinerary.gallery.length > 0"
      @click.self="showImageModal = false"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4"
    >
      <button
        @click="showImageModal = false"
        class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-3 text-white backdrop-blur transition hover:bg-white/20"
        aria-label="Close modal"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      
      <button
        v-if="itinerary.gallery.length > 1 && selectedImageIndex > 0"
        @click="selectedImageIndex = selectedImageIndex - 1"
        class="absolute left-4 z-10 rounded-full bg-white/10 p-3 text-white backdrop-blur transition hover:bg-white/20"
        aria-label="Previous image"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>
      
      <button
        v-if="itinerary.gallery.length > 1 && selectedImageIndex < itinerary.gallery.length - 1"
        @click="selectedImageIndex = selectedImageIndex + 1"
        class="absolute right-4 z-10 rounded-full bg-white/10 p-3 text-white backdrop-blur transition hover:bg-white/20"
        aria-label="Next image"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>

      <div class="relative max-h-[90vh] max-w-[90vw]">
        <img
          :src="itinerary.gallery[selectedImageIndex].url || itinerary.gallery[selectedImageIndex].cover"
          :alt="itinerary.gallery[selectedImageIndex].alt || itinerary.gallery[selectedImageIndex].name || 'Gallery image'"
          class="max-h-[90vh] max-w-[90vw] object-contain"
        />
        <div v-if="itinerary.gallery.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/60 px-4 py-2 text-sm text-white backdrop-blur">
          {{ selectedImageIndex + 1 }} / {{ itinerary.gallery.length }}
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-safari-sand">
      <div class="mx-auto max-w-6xl px-6 py-8 text-sm text-charcoal/70">
        <div class="flex flex-col gap-4 border-t border-safari-sand pt-6 md:flex-row md:items-center md:justify-between">
          <p>© {{ new Date().getFullYear() }} Go Tanzania Safari Ltd. All rights reserved.</p>
          <div class="flex gap-4">
            <a class="text-charcoal/70 hover:text-safari-gold" href="#">Instagram</a>
            <a class="text-charcoal/70 hover:text-safari-gold" href="#">Facebook</a>
            <a class="text-charcoal/70 hover:text-safari-gold" href="#">TripAdvisor</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

