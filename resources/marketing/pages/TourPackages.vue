<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

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
  gallery: Array<{ id: number; url: string; thumb: string; cover: string }>;
  images: string[];
};

const router = useRouter();
const packages = ref<TourPackage[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
  try {
    // Fetch all packages (no limit, show all)
    const response = await fetch('/api/tour-packages?per_page=50');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    // Handle paginated response
    const allPackages = Array.isArray(data) ? data : (data.data || []);
    
    // Map and sort packages (featured first, then by display_order)
    packages.value = allPackages
      .map((pkg: any) => ({
        id: pkg.id,
        slug: pkg.slug,
        title: pkg.title || '',
        short_description: pkg.short_description || null,
        description: pkg.description || '',
        price_from: pkg.price_from || null,
        duration_days: pkg.duration_days || null,
        max_participants: pkg.max_participants || null,
        is_featured: pkg.is_featured || false,
        hero_image: pkg.hero_image || null,
        gallery: pkg.gallery || [],
        images: pkg.images || [],
      }))
      .sort((a: TourPackage, b: TourPackage) => {
        // Featured packages first
        if (a.is_featured && !b.is_featured) return -1;
        if (!a.is_featured && b.is_featured) return 1;
        return 0;
      });
  } catch (err) {
    console.error('Error fetching tour packages:', err);
    error.value = 'Failed to load tour packages';
  } finally {
    isLoading.value = false;
  }
});

const goToPackage = (slug: string) => {
  router.push(`/tour-packages/${slug}`);
};
</script>

<template>
  <div class="min-h-screen bg-white text-charcoal">
    <!-- Header (same as ItineraryDetail) -->
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
            <router-link to="/#packages" class="transition hover:text-safari-gold hover:underline hover:underline-offset-8">Packages</router-link>
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

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-charcoal via-charcoal/95 to-charcoal py-20 sm:py-28">
      <div class="absolute inset-0 opacity-10">
        <img
          src="/images/safari/wildlife-savannah.jpg"
          alt=""
          class="h-full w-full object-cover"
          loading="lazy"
        />
      </div>
      <div class="relative mx-auto max-w-6xl px-6 text-center">
        <div class="inline-flex items-center gap-3 mb-6">
          <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
          <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">Tour Packages</p>
          <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
        </div>
        <h1 class="text-4xl font-heading font-bold text-white sm:text-5xl lg:text-6xl mb-6">
          Discover Our Exclusive Tour Packages
        </h1>
        <p class="text-lg sm:text-xl text-white/80 max-w-3xl mx-auto">
          Customize your perfect Tanzanian adventure with our flexible tour packages
        </p>
      </div>
    </section>

    <!-- Packages Grid -->
    <section class="py-16 sm:py-24 bg-gradient-to-b from-white via-safari-sand/5 to-white">
      <div class="mx-auto max-w-7xl px-6">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-20">
          <div class="mb-4 inline-block h-12 w-12 animate-spin rounded-full border-4 border-safari-gold border-t-transparent"></div>
          <p class="text-lg font-semibold text-charcoal">Loading packages...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-20">
          <p class="text-lg text-red-600 mb-4">{{ error }}</p>
          <button
            @click="router.push('/')"
            class="inline-flex items-center gap-2 rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90"
          >
            Back to Home
          </button>
        </div>

        <!-- Packages Grid -->
        <div v-else-if="packages.length > 0" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="pkg in packages"
            :key="pkg.id"
            @click="goToPackage(pkg.slug)"
            class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-safari-sand/30 bg-white/95 backdrop-blur-md shadow-xl transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-safari-gold/20 hover:border-safari-gold/50 cursor-pointer"
          >
            <!-- Image -->
            <div class="relative h-64 overflow-hidden">
              <img
                :src="pkg.hero_image?.cover || pkg.hero_image?.url || pkg.images?.[0] || '/images/safari/beach-1.jpg'"
                :alt="pkg.title"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                loading="lazy"
                @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
              
              <!-- Featured Badge -->
              <div v-if="pkg.is_featured" class="absolute left-6 top-6 z-10">
                <span class="inline-flex items-center rounded-full bg-safari-gold px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-charcoal shadow-glow-gold backdrop-blur-sm">
                  Featured
                </span>
              </div>

              <!-- Package Info Overlay -->
              <div class="absolute inset-x-6 bottom-6 z-10">
                <div class="flex items-center gap-3 text-sm font-semibold text-white mb-2">
                  <span v-if="pkg.duration_days" class="flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ pkg.duration_days }} days
                  </span>
                  <span v-if="pkg.max_participants" class="flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Up to {{ pkg.max_participants }} travelers
                  </span>
                </div>
                <h3 class="text-2xl font-heading font-bold text-white mb-2">{{ pkg.title }}</h3>
                <p v-if="pkg.price_from" class="text-lg font-bold text-safari-gold">
                  From ${{ Number(pkg.price_from).toLocaleString() }}
                </p>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 sm:p-8">
              <p v-if="pkg.short_description" class="text-charcoal/70 leading-relaxed line-clamp-3 mb-6">
                {{ pkg.short_description }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-safari-gold">View Details →</span>
                <span v-if="pkg.gallery && pkg.gallery.length > 0" class="text-xs text-charcoal/50">
                  {{ pkg.gallery.length }} photos
                </span>
              </div>
            </div>
          </article>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <p class="text-lg text-charcoal/70 mb-4">No tour packages available at the moment.</p>
          <router-link
            to="/#contact"
            class="inline-flex items-center gap-2 rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-safari-gold/90"
          >
            Contact Us
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

