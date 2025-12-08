<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import BookingForm from '../components/BookingForm.vue';

type GalleryImage = {
  id: number;
  url: string;
  thumb: string;
  cover: string;
  name: string;
  alt?: string;
};

type TourPackage = {
  id: number;
  slug: string;
  title: string;
  short_description: string | null;
  description: string;
  price_from: number | string | null;
  duration_days: number | null;
  max_participants: number | null;
  is_featured: boolean;
  hero_image: { url: string; thumb: string; cover: string } | null;
  gallery: GalleryImage[];
  images: string[];
};

const route = useRoute();
const router = useRouter();
const packageData = ref<TourPackage | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);
const selectedImageIndex = ref(0);
const showImageModal = ref(false);
const showBookingForm = ref(false);

onMounted(async () => {
  const slug = route.params.slug as string;
  
  try {
    const response = await fetch(`/api/tour-packages/${slug}`);
    if (!response.ok) {
      if (response.status === 404) {
        error.value = 'Tour package not found';
      } else {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return;
    }
    
    const data = await response.json();
    // Handle both paginated and single resource responses
    packageData.value = data.data || data;
  } catch (err) {
    console.error('Error fetching tour package:', err);
    error.value = 'Failed to load tour package details';
  } finally {
    isLoading.value = false;
  }
});

const openImageModal = (index: number) => {
  selectedImageIndex.value = index;
  showImageModal.value = true;
};

const closeImageModal = () => {
  showImageModal.value = false;
};

const nextImage = () => {
  if (packageData.value?.gallery) {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % packageData.value.gallery.length;
  }
};

const prevImage = () => {
  if (packageData.value?.gallery) {
    selectedImageIndex.value = (selectedImageIndex.value - 1 + packageData.value.gallery.length) % packageData.value.gallery.length;
  }
};

const handleBookingSuccess = () => {
  showBookingForm.value = false;
  // Show success message
  alert('Booking submitted successfully! We will contact you soon.');
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
            <router-link to="/tour-packages" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Packages</router-link>
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
        <p class="text-lg font-semibold text-charcoal">Loading package details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="relative flex min-h-screen items-center justify-center overflow-hidden bg-safari-sand/20">
      <div class="absolute inset-0 opacity-[0.15]">
        <img
          src="/images/safari/wildlife-giraffe.jpg"
          alt=""
          class="h-full w-full object-cover object-center"
          loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-white/30 via-white/15 to-white/30"></div>
      </div>
      <div class="relative z-10 text-center">
        <p class="text-xl font-semibold text-red-600 mb-6">{{ error }}</p>
        <button
          @click="router.push('/tour-packages')"
          class="inline-flex items-center gap-2 rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90"
        >
          Back to Packages
        </button>
      </div>
    </div>

    <!-- Package Content -->
    <div v-else-if="packageData" class="bg-white">
      <!-- Hero Image Section -->
      <section class="relative h-[60vh] min-h-[500px] overflow-hidden">
              <img
                v-if="packageData.hero_image?.cover || packageData.hero_image?.url || packageData.images?.[0]"
                :src="packageData.hero_image?.cover || packageData.hero_image?.url || packageData.images?.[0] || '/images/safari/beach-1.jpg'"
                :alt="packageData.title"
                class="h-full w-full object-cover"
                loading="eager"
                @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }"
              />
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/55 to-black/25"></div>
        <div class="absolute inset-0 flex items-end">
          <div class="mx-auto w-full max-w-6xl px-6 pb-16 text-white">
            <div v-if="packageData.is_featured" class="mb-4">
              <span class="inline-flex items-center rounded-full bg-safari-gold px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-charcoal shadow-glow-gold backdrop-blur-sm">
                Featured Package
              </span>
            </div>
            <h1 class="text-4xl font-heading font-bold sm:text-5xl lg:text-6xl mb-4">{{ packageData.title }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm">
              <span v-if="packageData.duration_days" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ packageData.duration_days }} days
              </span>
              <span v-if="packageData.max_participants" class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Up to {{ packageData.max_participants }} travelers
              </span>
              <span v-if="packageData.price_from" class="flex items-center gap-2 font-bold text-safari-gold">
                From ${{ Number(packageData.price_from).toLocaleString() }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <div class="mx-auto max-w-5xl px-6 py-16">
        <!-- Description -->
        <section class="mb-16">
          <div v-if="packageData.short_description" class="mb-6">
            <p class="text-xl text-charcoal/80 leading-relaxed">{{ packageData.short_description }}</p>
          </div>
          <div class="prose prose-lg max-w-none">
            <div v-html="packageData.description"></div>
          </div>
        </section>

        <!-- Photo Gallery -->
        <section v-if="packageData.gallery && packageData.gallery.length > 0" class="mb-16">
          <h2 class="text-3xl font-heading text-charcoal mb-6">Photo Gallery</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="(image, index) in packageData.gallery"
              :key="image.id"
              @click="openImageModal(index)"
              class="relative aspect-video overflow-hidden rounded-xl cursor-pointer group"
            >
              <img
                :src="image.cover || image.url"
                :alt="image.alt || image.name"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
              </div>
            </div>
          </div>
        </section>

        <!-- Booking CTA -->
        <section class="rounded-2xl bg-gradient-to-br from-safari-gold/10 via-safari-sand/5 to-safari-green/5 border border-safari-gold/20 p-8 sm:p-12 text-center">
          <h2 class="text-3xl font-heading font-bold text-charcoal mb-4">Ready to Book This Package?</h2>
          <p class="text-lg text-charcoal/70 mb-8 max-w-2xl mx-auto">
            Customize your perfect Tanzanian adventure. Choose your locations, duration, and number of travelers.
          </p>
          <button
            @click="showBookingForm = true"
            class="inline-flex items-center gap-2 rounded-full bg-safari-gold px-8 py-4 text-sm font-semibold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105"
          >
            Book This Package
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
          </button>
        </section>
      </div>
    </div>

    <!-- Image Modal -->
    <div
      v-if="showImageModal && packageData && packageData.gallery"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm"
      @click.self="closeImageModal"
    >
      <div class="relative max-w-5xl w-full h-[80vh] bg-gray-900 rounded-lg overflow-hidden shadow-2xl">
        <!-- Close Button -->
        <button
          @click="closeImageModal"
          class="absolute top-4 right-4 z-20 p-2 rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors"
          aria-label="Close image modal"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Image -->
        <img
          :src="packageData.gallery[selectedImageIndex]?.url"
          :alt="packageData.gallery[selectedImageIndex]?.alt || packageData.gallery[selectedImageIndex]?.name"
          class="w-full h-full object-contain"
        />

        <!-- Navigation -->
        <button
          @click="prevImage"
          v-if="packageData.gallery.length > 1"
          class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors"
          aria-label="Previous image"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <button
          @click="nextImage"
          v-if="packageData.gallery.length > 1"
          class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/20 text-white hover:bg-white/30 transition-colors"
          aria-label="Next image"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <!-- Image Counter -->
        <div
          v-if="packageData.gallery.length > 1"
          class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-black/50 text-white text-sm"
        >
          {{ selectedImageIndex + 1 }} / {{ packageData.gallery.length }}
        </div>
      </div>
    </div>

    <!-- Booking Form Modal -->
    <div
      v-if="showBookingForm && packageData"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
      @click.self="showBookingForm = false"
    >
      <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl">
        <button
          @click="showBookingForm = false"
          class="absolute top-4 right-4 z-20 p-2 rounded-full bg-charcoal/10 text-charcoal hover:bg-charcoal/20 transition-colors"
          aria-label="Close booking form"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <div class="p-6 sm:p-8">
          <BookingForm
            :package-id="packageData.id"
            :package-title="packageData.title"
            @success="handleBookingSuccess"
            @cancel="showBookingForm = false"
          />
        </div>
      </div>
    </div>
  </div>
</template>

