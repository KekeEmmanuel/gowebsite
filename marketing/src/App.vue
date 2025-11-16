<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

type HeroSlide = {
  image: string;
  label: string;
  title: string;
  description: string;
  ctaLabel: string;
  ctaHref: string;
};

const heroSlides: HeroSlide[] = [
  {
    image:
      '/images/safari/lioons.jpg',
    label: 'Tailored Luxury Adventures',
    title: 'Encounter Tanzania’s wild soul – from savannah to spice isles',
    description:
      'Join expert guides for once-in-a-lifetime safaris blending iconic wildlife moments, Kilimanjaro summits, and barefoot beach retreats.',
    ctaLabel: 'Explore Signature Safaris',
    ctaHref: '#safaris',
  },
  {
    image:
      '/images/safari/wildlife-zebra.jpg',
    label: 'Kilimanjaro Expeditions',
    title: 'Summit the roof of Africa with elite mountain crews',
    description:
      'Acclimatisation-focused itineraries, private mess tents, and seasoned expedition leaders guide every step toward Uhuru Peak.',
    ctaLabel: 'Plan Your Climb',
    ctaHref: '#safaris',
  },
  {
    image:
      '/images/safari/wildlife-giraffe.jpg',
    label: 'Zanzibar Beach Retreats',
    title: 'Unwind on Zanzibar’s spice-scented shores',
    description:
      'Blend Stone Town heritage strolls with villa-style resorts fringed by coconut palms and gin-clear lagoons.',
    ctaLabel: 'Design My Escape',
    ctaHref: '#lodges',
  },
];

const currentSlide = ref(0);
const activeSlide = computed<HeroSlide>(() => (heroSlides[currentSlide.value] || heroSlides[0]) as HeroSlide);

let intervalId: ReturnType<typeof setInterval> | undefined;

const startAutoSlide = () => {
  intervalId = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % heroSlides.length;
  }, 7000);
};

const restartAutoSlide = () => {
  if (intervalId) {
    clearInterval(intervalId);
  }
  startAutoSlide();
};

const goToSlide = (index: number) => {
  currentSlide.value = index;
  restartAutoSlide();
};

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % heroSlides.length;
  restartAutoSlide();
};

const prevSlide = () => {
  currentSlide.value =
    (currentSlide.value - 1 + heroSlides.length) % heroSlides.length;
  restartAutoSlide();
};

onMounted(() => {
  startAutoSlide();
});

onBeforeUnmount(() => {
  if (intervalId) {
    clearInterval(intervalId);
  }
});

const safariPackages = [
  {
    title: 'Great Migration Serengeti Safari',
    summary:
      'Witness river crossings and endless savannah herds with private guides.',
    meta: '7 days · Serengeti &amp; Grumeti',
    image:
      '/images/safari/wildlife-zebra.jpg',
  },
  {
    title: 'Kilimanjaro Machame Summit Trek',
    summary:
      'Small-group climbs with acclimatisation camps and expert mountain crew.',
    meta: '8 days · Machame Route',
    image:
      '/images/safari/wildlife-giraffe.jpg',
  },
  {
    title: 'Zanzibar Spice &amp; Beach Escape',
    summary:
      'Boutique Stone Town stays paired with barefoot luxury on Nungwi sands.',
    meta: '5 days · Zanzibar Archipelago',
    image:
      '/images/safari/beach-1.jpg',
  },
];

const destinationSpots = [
  {
    name: 'Serengeti National Park',
    tag: 'Great Migration',
    image:
      '/images/safari/wildlife-savannah.jpg',
  },
  {
    name: 'Ngorongoro Crater',
    tag: 'World Heritage',
    image:
      '/images/safari/wildlife-herd.jpg',
  },
  {
    name: 'Mount Kilimanjaro',
    tag: 'Summit Trek',
    image:
      '/images/safari/beach-2.jpg',
  },
  {
    name: 'Zanzibar Beaches',
    tag: 'Spice Island',
    image:
      '/images/safari/beach-2.jpg',
  },
];

const signatureLodges = [
  {
    name: 'Four Seasons Safari Lodge',
    location: 'Serengeti Plains',
    image:
      '/images/safari/lodge-1.jpg',
  },
  {
    name: 'Gibb’s Farm Manor House',
    location: 'Ngorongoro Highlands',
    image:
      '/images/safari/lodge-2.jpg',
  },
  {
    name: 'The Residence Zanzibar',
    location: 'Kizimkazi Peninsula',
    image:
      '/images/safari/beach-3.jpg',
  },
];
</script>

<template>
  <div class="min-h-screen bg-white text-charcoal">
    <header class="sticky top-0 z-50 bg-black/70 backdrop-blur">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a class="text-2xl font-heading text-white" href="#home">
          Go Tanzania Safari Ltd
        </a>
        <nav class="hidden gap-6 text-sm font-medium lg:flex">
          <a class="text-white hover:text-safari-gold" href="#home">Home</a>
          <a class="text-white hover:text-safari-gold" href="#about">About</a>
          <a class="text-white hover:text-safari-gold" href="#safaris">Safaris</a>
          <a class="text-white hover:text-safari-gold" href="#destinations">Destinations</a>
          <a class="text-white hover:text-safari-gold" href="#lodges">Lodges</a>
          <a class="text-white hover:text-safari-gold" href="#contact">Contact</a>
        </nav>
        <button
          type="button"
          class="hidden rounded-full border border-white/40 px-5 py-2 text-sm font-semibold  text-white transition hover:bg-white hover:text-charcoal lg:inline-flex"
        >
          Book Your Safari
        </button>
      </div>
    </header>

    <main>
      <section id="home" class="relative">
        <div class="relative h-[70vh] min-h-[520px]">
          <div
            v-for="(slide, index) in heroSlides"
            :key="slide.image"
            class="absolute inset-0 transition-opacity duration-700"
            :class="[
              currentSlide === index ? 'opacity-100' : 'pointer-events-none opacity-0',
            ]"
          >
            <img
              :src="slide.image"
              :alt="slide.title"
              class="h-full w-full object-cover"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/55 to-black/25"></div>
          </div>

          <div class="relative z-10 mx-auto flex h-full max-w-6xl flex-col justify-end px-6 pb-20 pt-28 text-white">
            <p class="text-sm uppercase tracking-[0.4em] text-safari-gold">
              {{ activeSlide.label }}
            </p>
            <h1 class="mt-4 max-w-3xl text-4xl font-heading leading-tight sm:text-5xl">
              {{ activeSlide.title }}
            </h1>
            <p class="mt-6 max-w-2xl text-base text-white/80">
              {{ activeSlide.description }}
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
              <a
                :href="activeSlide.ctaHref"
                class="rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-white"
              >
                {{ activeSlide.ctaLabel }}
              </a>
              <a
                href="#destinations"
                class="rounded-full border border-safari-gold px-6 py-3 text-sm font-semibold text-safari-gold transition hover:bg-safari-gold hover:text-charcoal"
              >
                View Destinations
              </a>
            </div>
          </div>

          <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

          <div class="absolute inset-x-0 bottom-8 z-20 flex items-center justify-between px-6">
            <div class="flex gap-3">
              <button
                type="button"
                class="pointer-events-auto rounded-full border border-white/40 bg-black/40 p-3 text-white transition hover:bg-black/70"
                @click="prevSlide"
                aria-label="Previous slide"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
              </button>
              <button
                type="button"
                class="pointer-events-auto rounded-full border border-white/40 bg-black/40 p-3 text-white transition hover:bg-black/70"
                @click="nextSlide"
                aria-label="Next slide"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5" />
                </svg>
              </button>
            </div>
            <div class="pointer-events-auto flex gap-2">
              <button
                v-for="(slide, index) in heroSlides"
                :key="slide.image + index"
                type="button"
                class="h-2.5 w-8 rounded-full transition"
                :class="currentSlide === index ? 'bg-safari-gold' : 'bg-white/40 hover:bg-white/70'"
                @click="goToSlide(index)"
                :aria-label="`Go to slide ${index + 1}`"
              ></button>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-safari-sand/40 py-16">
        <div class="mx-auto max-w-4xl px-6 text-center">
          <p class="text-xs font-semibold uppercase tracking-[0.4em] text-safari-gold">
            Our Promise
          </p>
          <h2 class="mt-3 text-3xl font-heading text-safari-green">
            Why Travel with Go Tanzania Safari
          </h2>
          <p class="mt-4 text-base text-charcoal/70">
            We blend decades of on-the-ground knowledge with bespoke planning to deliver effortless journeys beyond the guidebooks.
          </p>
        </div>
        <div class="mx-auto mt-12 grid max-w-6xl gap-10 px-6 md:grid-cols-3">
          <article class="text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-safari-gold/80 bg-white text-safari-gold">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 16v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="3" stroke-linecap="round" stroke-linejoin="round" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a3.01 3.01 0 0 1 0 5.74" />
              </svg>
            </div>
            <h3 class="mt-6 text-lg font-semibold tracking-wide text-charcoal">
              500+ Happy Travellers Yearly
            </h3>
            <div class="mx-auto mt-3 h-px w-12 bg-safari-gold/60"></div>
            <p class="mt-4 text-sm leading-relaxed text-charcoal/70">
              Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.
            </p>
          </article>
          <article class="text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-safari-gold/80 bg-white text-safari-gold">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v22" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 5.5C17 3.57 15.43 2 13.5 2h-3C8.57 2 7 3.57 7 5.5S8.57 9 10.5 9h3C15.43 9 17 10.57 17 12.5S15.43 16 13.5 16h-3C8.57 16 7 17.57 7 19.5 7 21.43 8.57 23 10.5 23h3C15.43 23 17 21.43 17 19.5" />
              </svg>
            </div>
            <h3 class="mt-6 text-lg font-semibold tracking-wide text-charcoal">
              Best Price Guarantee
            </h3>
            <div class="mx-auto mt-3 h-px w-12 bg-safari-gold/60"></div>
            <p class="mt-4 text-sm leading-relaxed text-charcoal/70">
              Preferred partner rates across lodges, charter flights, and experiences tailored to your budget.
            </p>
          </article>
          <article class="text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-safari-gold/80 bg-white text-safari-gold">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v5a2.5 2.5 0 0 1-2.5 2.5H13l-4.5 4v-4H6.5A2.5 2.5 0 0 1 4 12.5v-5Z" />
              </svg>
            </div>
            <h3 class="mt-6 text-lg font-semibold tracking-wide text-charcoal">
              24/7 Top-Notch Support
            </h3>
            <div class="mx-auto mt-3 h-px w-12 bg-safari-gold/60"></div>
            <p class="mt-4 text-sm leading-relaxed text-charcoal/70">
              Dedicated concierge before, during, and after your safari—always a WhatsApp or call away.
            </p>
          </article>
        </div>
      </section>

      <section id="about" class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-12">
          <div class="lg:col-span-5">
            <h2 class="text-3xl font-heading  text-white">Why Travel with Us</h2>
            <p class="mt-4 text-sm uppercase tracking-[0.3em] text-safari-gold">
              Authentic Tanzanian Expertise
            </p>
          </div>
          <div class="lg:col-span-7 space-y-6 text-base text-charcoal/80">
            <p>
              Our locally rooted team orchestrates bespoke journeys that balance legendary game viewing,
              cultural immersion, and sustainable luxury. We personally vet camps, pilots, and guides to
              ensure every detail is in harmony with Tanzania’s rhythm.
            </p>
            <p>
              From private charters into the Serengeti to exclusive spice farm dinners on Zanzibar, we’re
              dedicated to crafting stories you’ll share for a lifetime.
            </p>
          </div>
        </div>
      </section>

      <section id="safaris" class="bg-safari-sand/60 py-16">
        <div class="mx-auto max-w-6xl px-6">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <h2 class="text-3xl font-heading  text-white">Signature Safaris</h2>
              <p class="mt-2 text-charcoal/70">
                Hand-selected itineraries featuring Tanzania’s most captivating wildlife encounters and
                landscapes.
              </p>
            </div>
            <a
              href="#contact"
              class="self-start rounded-full border border-white/40 px-5 py-2 text-sm font-semibold  text-white transition hover:bg-white hover:text-charcoal"
            >
              Customise Your Journey
            </a>
          </div>
          <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <article
              v-for="safari in safariPackages"
              :key="safari.title"
              class="group rounded-3xl border border-white bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >
              <div class="overflow-hidden rounded-2xl">
                <img
                  :src="safari.image"
                  :alt="safari.title"
                  class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105"
                  loading="lazy"
                />
              </div>
              <h3 class="mt-6 font-heading text-xl  text-white" v-html="safari.title"></h3>
              <p class="mt-3 text-sm text-charcoal/70" v-html="safari.meta"></p>
              <p class="mt-3 text-sm text-charcoal/80">{{ safari.summary }}</p>
              <button class="mt-4 text-sm font-semibold text-safari-gold transition hover:text-safari-gold">
                View Details →
              </button>
            </article>
          </div>
        </div>
      </section>

      <section id="destinations" class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <h2 class="text-3xl font-heading  text-white">Iconic Destinations</h2>
            <p class="mt-2 text-charcoal/70">
              From volcanic calderas to turquoise coastlines – discover the regions that define Tanzania.
            </p>
          </div>
          <a
            href="#lodges"
            class="self-start rounded-full bg-safari-green px-5 py-2 text-sm font-semibold text-white transition hover:bg-charcoal"
          >
            See Where You’ll Stay
          </a>
        </div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="spot in destinationSpots"
            :key="spot.name"
            class="group relative overflow-hidden rounded-3xl"
          >
            <img
              :src="spot.image"
              :alt="spot.name"
              class="aspect-square w-full object-cover transition duration-500 group-hover:scale-110"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-4 text-white">
              <h3 class="font-heading text-lg">{{ spot.name }}</h3>
              <p class="text-xs uppercase tracking-widest text-safari-gold">{{ spot.tag }}</p>
            </div>
          </div>
        </div>
      </section>

      <section id="lodges" class="bg-white py-16">
        <div class="mx-auto max-w-6xl px-6">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <h2 class="text-3xl font-heading  text-white">Luxury Lodges &amp; Camps</h2>
              <p class="mt-2 text-charcoal/70">
                Stay in hand-picked retreats that blend world-class comfort with authentic safari spirit.
              </p>
            </div>
            <a
              href="#contact"
              class="self-start rounded-full border border-safari-gold px-5 py-2 text-sm font-semibold text-safari-gold transition hover:bg-safari-gold hover:text-charcoal"
            >
              Request Availability
            </a>
          </div>
          <div class="mt-10 grid gap-6 md:grid-cols-3">
            <article
              v-for="lodge in signatureLodges"
              :key="lodge.name"
              class="overflow-hidden rounded-3xl border border-safari-sand/70 bg-white shadow-sm"
            >
              <img
                :src="lodge.image"
                :alt="lodge.name"
                class="aspect-[4/3] w-full object-cover"
                loading="lazy"
              />
              <div class="p-6">
                <h3 class="font-heading text-xl  text-white">{{ lodge.name }}</h3>
                <p class="mt-2 text-sm text-charcoal/70">{{ lodge.location }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section id="contact" class="bg-charcoal text-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6 py-16 lg:flex-row">
          <div class="lg:w-1/2">
            <h2 class="text-3xl font-heading text-safari-gold">
              Plan Your Tailor-Made Safari
            </h2>
            <p class="mt-4 text-white/80">
              Share your travel dreams and our specialists will design a personalised itinerary within 24
              hours.
            </p>
            <div class="mt-6 space-y-2 text-sm text-white/70">
              <p>Phone: +255 (0) 123 456 789</p>
              <p>Email: bookings@gotanzaniasafari.com</p>
              <p>Office: Arusha, Tanzania</p>
            </div>
          </div>
          <form class="space-y-4 rounded-3xl bg-white/5 p-6 backdrop-blur lg:w-1/2">
            <div>
              <label class="text-sm font-semibold text-safari-gold" for="fullname">
                Name
              </label>
              <input
                id="fullname"
                type="text"
                placeholder="Your full name"
                class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
              />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-sm font-semibold text-safari-gold" for="email">
                  Email
                </label>
                <input
                  id="email"
                  type="email"
                  placeholder="you@example.com"
                  class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                />
              </div>
              <div>
                <label class="text-sm font-semibold text-safari-gold" for="phone">
                  Phone
                </label>
                <input
                  id="phone"
                  type="tel"
                  placeholder="+255..."
                  class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                />
              </div>
            </div>
            <div>
              <label class="text-sm font-semibold text-safari-gold" for="service">
                Interested Service
              </label>
              <select
                id="service"
                class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
              >
                <option class="text-charcoal" value="">Select</option>
                <option class="text-charcoal" value="wildlife">Wildlife Safari</option>
                <option class="text-charcoal" value="kili">Kilimanjaro Trek</option>
                <option class="text-charcoal" value="zanzibar">Zanzibar Escape</option>
              </select>
            </div>
            <div>
              <label class="text-sm font-semibold text-safari-gold" for="message">
                Message
              </label>
              <textarea
                id="message"
                rows="4"
                placeholder="Tell us about your ideal safari experience..."
                class="mt-1 w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
              ></textarea>
            </div>
            <button
              type="submit"
              class="w-full rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-white"
            >
              Submit Inquiry
            </button>
          </form>
        </div>
      </section>
    </main>

    <footer class="bg-white">
      <div class="mx-auto max-w-6xl px-6 py-8 text-sm text-charcoal/70">
        <div class="flex flex-col gap-4 border-t border-safari-sand pt-6 md:flex-row md:items-center md:justify-between">
          <p>© {{ new Date().getFullYear() }} Go Tanzania Safari Ltd. All rights reserved.</p>
          <div class="flex gap-4">
            <a class="text-white hover:text-safari-gold" href="#">Instagram</a>
            <a class="text-white hover:text-safari-gold" href="#">Facebook</a>
            <a class="text-white hover:text-safari-gold" href="#">TripAdvisor</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
