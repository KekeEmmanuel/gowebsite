<script setup lang="ts">
import { computed, h, onBeforeUnmount, onMounted, ref } from 'vue';

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
      '/images/safari/moses-londo-5Lm1A5vxczc-unsplash.jpg',
    label: 'Tailored Luxury Adventures',
    title: 'Encounter Tanzania’s wild soul – from savannah to spice isles',
    description:
      'Join expert guides for once-in-a-lifetime safaris blending iconic wildlife moments, Kilimanjaro summits, and barefoot beach retreats.',
    ctaLabel: 'Explore Signature Safaris',
    ctaHref: '#safaris',
  },
  {
    image:
      '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
    label: 'Kilimanjaro Expeditions',
    title: 'Summit the roof of Africa with elite mountain crews',
    description:
      'Acclimatisation-focused itineraries, private mess tents, and seasoned expedition leaders guide every step toward Uhuru Peak.',
    ctaLabel: 'Plan Your Climb',
    ctaHref: '#safaris',
  },
  {
    image:
      '/images/safari/jack-balke-tgDNgeW7nGY-unsplash.jpg',
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

  featureObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        featureVisible.value = true;
        travellerCount.value = 0;
        animateTravellerCount(TARGET_TRAVELLERS);
        if (featureObserver) {
          featureObserver.disconnect();
        }
      }
    },
    { threshold: 0.3 },
  );

  if (featureSection.value) {
    featureObserver.observe(featureSection.value);
  }
});

onBeforeUnmount(() => {
  if (intervalId) {
    clearInterval(intervalId);
  }

  if (featureObserver) {
    featureObserver.disconnect();
  }

  if (countAnimationFrame) {
    cancelAnimationFrame(countAnimationFrame);
    countAnimationFrame = null;
  }
});

const safariPackages = [
  {
    title: 'Great Migration Serengeti Safari',
    summary:
      'Follow the famed wildebeest march with private 4x4 game drives, exclusive viewing decks, and optional sunrise balloon flights.',
    meta: '7 days · Serengeti &amp; Grumeti',
    image:
      '/images/safari/wildlife-zebra.jpg',
    badge: 'Signature Collection',
    highlights: [
      'Luxury tented camps positioned on migration corridors',
      'Dedicated photographic guide &amp; spotter team',
      'Champagne brunch set on the savannah rim',
    ],
  },
  {
    title: 'Kilimanjaro Machame Summit Trek',
    summary:
      'Pole pole ascent that blends private acclimatisation camps, chef-prepared cuisine, and summit-day oxygen support.',
    meta: '8 days · Machame Route',
    image:
      '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
    badge: 'Expedition Grade',
    highlights: [
      'Maximum 8 climbers per departure',
      'Hyperbaric chamber &amp; medical lead on trek',
      'Celebratory stay at a boutique Arusha manor',
    ],
  },
  {
    title: 'Zanzibar Spice &amp; Beach Escape',
    summary:
      'Taste Stone Town’s spice heritage then drift between private villa resorts, dhow cruises, and reef snorkelling.',
    meta: '6 days · Zanzibar Archipelago',
    image:
      '/images/safari/beach-1.jpg',
    badge: 'Coastal Indulgence',
    highlights: [
      'Guided Stone Town heritage &amp; culinary tour',
      'Private sunset dhow with live taarab music',
      'Wellness rituals at oceanside spa pavilions',
    ],
  },
];

const destinationSpots = [
  {
    name: 'Serengeti National Park',
    tag: 'Great Migration',
    description: 'River-crossing drama, predator action, endless horizons under technicolour sunsets.',
    image:
      '/images/safari/wildlife-savannah.jpg',
  },
  {
    name: 'Ngorongoro Crater',
    tag: 'World Heritage',
    description: 'A collapsed caldera teeming with BIG5 sightings, Maasai culture, and mist-draped mornings.',
    image:
      '/images/safari/wildlife-herd.jpg',
  },
  {
    name: 'Mount Kilimanjaro',
    tag: 'Summit Trek',
    description: 'Africa’s rooftop crowned by glaciers, alpine desert moonscapes, and iconic Uhuru sunrise.',
    image:
      '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
  },
  {
    name: 'Ruaha &amp; Selous Reserves',
    tag: 'Southern Circuit',
    description: 'Remote fly-in safaris with boating on the Rufiji, walking trails, and off-grid luxury camps.',
    image:
      '/images/safari/wildlife-giraffe.jpg',
  },
  {
    name: 'Zanzibar Archipelago',
    tag: 'Coastal Haven',
    description: 'From Matemwe reefs to Mnemba atoll, drift between spice farms and barefoot luxury hideaways.',
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
    mood: 'Waterhole-facing infinity pools &amp; spa sanctuaries in the savannah canopy.',
  },
  {
    name: 'Gibb’s Farm Manor House',
    location: 'Ngorongoro Highlands',
    image:
      '/images/safari/lodge-2.jpg',
    mood: 'Artist cottages, organic farm-to-table dining, and valley views wrapped in coffee estates.',
  },
  {
    name: 'The Residence Zanzibar',
    location: 'Kizimkazi Peninsula',
    image:
      '/images/safari/beach-3.jpg',
    mood: 'Private pool villas, butler-led service, and azure lagoons inspired by Swahili heritage.',
  },
  {
    name: 'Chem Chem Lodge',
    location: 'Lake Manyara Corridor',
    image:
      '/images/safari/alferio-njau-MESNFA-pINg-unsplash.jpg',
    mood: 'Slow safari philosophy with sunrise yoga decks, Maasai-led walks, and flamingo-dusted vistas.',
  },
];

const aboutStats = [
  { value: '18+', label: 'Years curating luxury safaris' },
  { value: '32', label: 'Handpicked camps &amp; lodges' },
  { value: '96%', label: 'Guest satisfaction rating' },
];

const aboutHighlights = [
  {
    title: 'Dedicated Journey Architect',
    copy:
      'A single expert consultant curates, books, and monitors every detail from the first call to your return home.',
  },
  {
    title: 'Conservation First',
    copy:
      'Partnerships with community conservancies, carbon offset initiatives, and low-impact travel practices.',
  },
  {
    title: 'Seamless Logistics',
    copy:
      'Private charters, VIP fast-track on arrival, and on-call concierges ensure your itinerary flows effortlessly.',
  },
];

const contactChannels = [
  {
    label: 'Call',
    value: '+255 (0) 742 123 456',
    detail: 'Daily 08:00 – 20:00 East Africa Time',
  },
  {
    label: 'Email',
    value: 'bookings@gotanzaniasafari.com',
    detail: 'Expect a crafted itinerary within 24 hours',
  },
  {
    label: 'WhatsApp',
    value: '+255 (0) 742 123 456',
    detail: 'Instant updates &amp; on-trip assistance',
  },
];

const contactQuickFacts = [
  'Dedicated concierge from pre-trip briefing to touchdown back home.',
  'Access to a private guest portal with live itinerary updates.',
  'Emergency response network spanning Tanzania and Zanzibar.',
];

const featureSection = ref<HTMLElement | null>(null);
const featureVisible = ref(false);
const travellerCount = ref(0);
const TARGET_TRAVELLERS = 500;
let featureObserver: IntersectionObserver | null = null;
let countAnimationFrame: number | null = null;

const featureCards = [
  {
    icon: 'travellers',
    title: 'Happy Travellers Yearly',
    headline: '500+ Happy Travellers Yearly',
    copy:
      'Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.',
  },
  {
    icon: 'pricing',
    title: 'Best Price Guarantee',
    copy:
      'Preferred partner rates across lodges, charter flights, and experiences tailored to your budget.',
  },
  {
    icon: 'support',
    title: '24/7 Top-Notch Support',
    copy:
      'Dedicated concierge before, during, and after your safari—always a WhatsApp or call away.',
  },
] as const;

const easeOutQuad = (t: number) => 1 - (1 - t) * (1 - t);

const animateTravellerCount = (to: number, duration = 1200) => {
  if (countAnimationFrame) {
    cancelAnimationFrame(countAnimationFrame);
  }

  const from = travellerCount.value;
  const start = performance.now();

  const step = (now: number) => {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = easeOutQuad(progress);
    travellerCount.value = Math.round(from + (to - from) * eased);

    if (progress < 1) {
      countAnimationFrame = requestAnimationFrame(step);
    } else {
      countAnimationFrame = null;
    }
  };

  countAnimationFrame = requestAnimationFrame(step);
};

const boostTravellerCount = () => {
  animateTravellerCount(TARGET_TRAVELLERS + 40, 400);
};

const settleTravellerCount = () => {
  animateTravellerCount(TARGET_TRAVELLERS, 600);
};

const TravellersIcon = () =>
  h(
    'svg',
    {
      xmlns: 'http://www.w3.org/2000/svg',
      viewBox: '0 0 82 82',
      fill: 'none',
      class: 'h-14 w-14 text-safari-gold',
    },
    [
      h('circle', { cx: 41, cy: 41, r: 40, stroke: 'currentColor', 'stroke-width': 2 }),
      h('path', {
        d: 'M30 34c0-6.075 4.925-11 11-11s11 4.925 11 11-4.925 11-11 11-11-4.925-11-11Z',
        stroke: 'currentColor',
        'stroke-width': 2,
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
      }),
      h('path', {
        d: 'M23 59.5c1.8-9.4 9.467-16 18-16s16.2 6.6 18 16',
        stroke: 'currentColor',
        'stroke-width': 2,
        'stroke-linecap': 'round',
      }),
    ],
  );

const PricingIcon = () =>
  h(
    'svg',
    {
      xmlns: 'http://www.w3.org/2000/svg',
      viewBox: '0 0 82 82',
      fill: 'none',
      class: 'h-14 w-14 text-safari-gold',
    },
    [
      h('circle', { cx: 41, cy: 41, r: 40, stroke: 'currentColor', 'stroke-width': 2 }),
      h('path', {
        d: 'M50 29H36.5a5.5 5.5 0 0 0 0 11h9a5.5 5.5 0 0 1 0 11H28',
        stroke: 'currentColor',
        'stroke-width': 2,
        'stroke-linecap': 'round',
      }),
      h('path', {
        d: 'M41 21v40',
        stroke: 'currentColor',
        'stroke-width': 2,
        'stroke-linecap': 'round',
      }),
    ],
  );

const SupportIcon = () =>
  h(
    'svg',
    {
      xmlns: 'http://www.w3.org/2000/svg',
      viewBox: '0 0 82 82',
      fill: 'none',
      class: 'h-14 w-14 text-safari-gold',
    },
    [
      h('circle', { cx: 41, cy: 41, r: 40, stroke: 'currentColor', 'stroke-width': 2 }),
      h('path', {
        d: 'M26 50.5V36.8c0-6.8 5.5-12.3 12.3-12.3h5.4c6.8 0 12.3 5.5 12.3 12.3v13.7c0 .7-.3 1.3-.8 1.8l-7.6 6.6c-1 1-2.8.3-2.8-1.1v-4.6h-7.5c-6.8 0-12.3-5.5-12.3-12.3',
        stroke: 'currentColor',
        'stroke-width': 2,
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
      }),
    ],
  );

const featureIcons = {
  travellers: TravellersIcon,
  pricing: PricingIcon,
  support: SupportIcon,
} as const;

const getFeatureIcon = (key: keyof typeof featureIcons) => featureIcons[key];
</script>

<template>
  <div class="min-h-screen bg-white text-charcoal">
    <header class="sticky top-0 z-50 bg-black/85 backdrop-blur">
      <div class="mx-auto grid w-full max-w-7xl grid-cols-[minmax(240px,500px)_1fr_auto] items-center gap-4 px-4 py-2.5 lg:gap-8 lg:px-8">
        <a class="flex items-center justify-start" href="#home">
          <img
            src="/images/safari/mpya.png"
            alt="Go Tanzania Safari"
            class="h-[6.5rem] w-full max-w-[500px] object-contain lg:h-[7rem]"
            loading="lazy"
          />
        </a>
        <div class="hidden items-center justify-center lg:flex">
          <nav class="flex items-center gap-4 rounded-full border border-white/10 bg-white/5 px-5 py-2 text-[11px] font-semibold uppercase tracking-[0.36em] text-white/70">
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#home">Home</a>
            <span class="h-3.5 w-px bg-white/15"></span>
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#about">About</a>
            <span class="h-3.5 w-px bg-white/15"></span>
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#safaris">Safaris</a>
            <span class="h-3.5 w-px bg-white/15"></span>
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#destinations">Destinations</a>
            <span class="h-3.5 w-px bg-white/15"></span>
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#lodges">Lodges</a>
            <span class="h-3.5 w-px bg-white/15"></span>
            <a class="transition hover:text-safari-gold hover:underline hover:underline-offset-8" href="#contact">Contact</a>
          </nav>
        </div>
        <div class="flex items-center justify-end gap-3">
          <a
            href="#contact"
            class="hidden rounded-full border border-safari-gold px-5 py-1.5 text-[11px] font-semibold uppercase tracking-[0.36em] text-safari-gold transition hover:bg-safari-gold hover:text-charcoal lg:inline-flex"
          >
            Start Planning
          </a>
          <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-white transition hover:border-safari-gold hover:text-safari-gold lg:hidden"
            aria-label="Open navigation"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
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

      <section
        ref="featureSection"
        :class="[
          'bg-safari-sand/40 py-16 transition-all duration-700 ease-out',
          featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10',
        ]"
      >
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
          <article
            v-for="(feature, index) in featureCards"
            :key="feature.title"
            class="group rounded-3xl border border-safari-sand/70 bg-white/80 p-8 text-center transition-all duration-700 ease-out backdrop-blur hover:-translate-y-3 hover:shadow-xl"
            :class="featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
            :style="{ transitionDelay: featureVisible ? `${index * 120}ms` : '0ms' }"
            @mouseenter="feature.icon === 'travellers' && boostTravellerCount()"
            @mouseleave="feature.icon === 'travellers' && settleTravellerCount()"
          >
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-safari-gold/80 bg-white text-safari-gold">
              <component :is="getFeatureIcon(feature.icon)" />
            </div>
            <template v-if="feature.icon === 'travellers'">
              <p class="mt-6 text-4xl font-heading text-safari-green">
                {{ travellerCount.toLocaleString() }}
                <span class="text-2xl font-semibold text-safari-gold">+</span>
              </p>
              <h3 class="mt-2 text-lg font-semibold tracking-wide text-charcoal">
                {{ feature.title }}
              </h3>
            </template>
            <template v-else>
              <h3 class="mt-6 text-lg font-semibold tracking-wide text-charcoal">
                {{ feature.title }}
              </h3>
            </template>
            <div class="mx-auto mt-3 h-px w-12 bg-safari-gold/60"></div>
            <p class="mt-4 text-sm leading-relaxed text-charcoal/70">
              {{ feature.copy }}
            </p>
          </article>
        </div>
      </section>

      <section id="about" class="relative overflow-hidden bg-white py-20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(217,154,56,0.08),_transparent_55%)]"></div>
        <div class="relative mx-auto max-w-6xl px-6">
          <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                Authentic Tanzanian Expertise
              </p>
              <h2 class="mt-4 text-4xl font-heading text-charcoal sm:text-5xl">
                Journeys designed by locals who live, breathe, and protect Tanzania
              </h2>
              <p class="mt-6 text-base leading-relaxed text-charcoal/80">
                We move beyond brochure itineraries. Our Dar es Salaam and Arusha teams collaborate daily
                with guides, lodge owners, and conservation partners to secure privileged access and real-time
                intelligence. The result: safaris that feel effortless, immersive, and entirely your own.
              </p>
              <div class="mt-8 flex flex-wrap gap-4">
                <a
                  href="#contact"
                  class="rounded-full bg-safari-green px-6 py-3 text-sm font-semibold text-white transition hover:bg-charcoal"
                >
                  Speak to a Journey Architect
                </a>
                <a
                  href="#safaris"
                  class="rounded-full border border-safari-green px-6 py-3 text-sm font-semibold text-safari-green transition hover:bg-safari-green hover:text-white"
                >
                  View Sample Safaris
                </a>
              </div>
            </div>
            <div class="rounded-3xl bg-white/70 p-8 shadow-xl ring-1 ring-safari-sand/40 backdrop-blur">
              <ul class="grid gap-6 sm:grid-cols-3 lg:grid-cols-1">
                <li
                  v-for="stat in aboutStats"
                  :key="stat.label"
                  class="rounded-2xl bg-safari-sand/30 px-6 py-5 text-center shadow-sm shadow-safari-sand/30"
                >
                  <p class="text-3xl font-heading text-safari-green">{{ stat.value }}</p>
                  <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-charcoal/70" v-html="stat.label"></p>
                </li>
              </ul>
              <div class="mt-8 rounded-2xl border border-safari-sand/60 bg-safari-sand/20 p-6">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-safari-gold">Accreditations</p>
                <p class="mt-3 text-sm leading-relaxed text-charcoal/75">
                  Proud members of the Tanzania Association of Tour Operators (TATO), ATTA, and Leave No Trace. We
                  hand-select suppliers championing eco-conscious luxury.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-14 grid gap-6 md:grid-cols-3">
            <article
              v-for="highlight in aboutHighlights"
              :key="highlight.title"
              class="group rounded-3xl border border-safari-sand/50 bg-white/80 p-6 transition hover:-translate-y-2 hover:border-safari-gold hover:shadow-xl"
            >
              <div class="inline-flex rounded-full bg-safari-sand/60 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-safari-green">
                Core Pillar
              </div>
              <h3 class="mt-4 text-xl font-heading text-charcoal">{{ highlight.title }}</h3>
              <p class="mt-3 text-sm leading-relaxed text-charcoal/75">
                {{ highlight.copy }}
              </p>
              <div class="mt-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                Learn More
                <span aria-hidden="true">→</span>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section id="safaris" class="relative overflow-hidden bg-safari-green py-20 text-white">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(15,37,25,0.85),rgba(31,59,43,0.65))]"></div>
        <div class="absolute inset-0 opacity-50 mix-blend-screen" style="background-image: url('/images/safari/wildlife-herd.jpg'); background-size: cover; background-position: center;"></div>
        <div class="relative mx-auto max-w-6xl px-6">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.4em] text-safari-gold">
                Signature Safaris
              </p>
              <h2 class="mt-4 text-4xl font-heading text-white sm:text-5xl">
                Preview a few of our most-requested tailored itineraries
              </h2>
              <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/70">
                Every journey is individually reimagined around your pace, interests, and travel style. Use these curated
                blueprints as inspiration—our team refines them into a completely bespoke adventure.
              </p>
            </div>
            <a
              href="#contact"
              class="inline-flex items-center gap-3 self-start rounded-full bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-safari-green"
            >
              Plan a Custom Safari
              <span aria-hidden="true">→</span>
            </a>
          </div>
          <div class="mt-12 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="grid gap-6 md:grid-cols-2">
              <article
                v-for="safari in safariPackages"
                :key="safari.title"
                class="group flex h-full flex-col overflow-hidden rounded-3xl bg-white/10 shadow-xl shadow-black/20 transition hover:-translate-y-2 hover:bg-white/15"
              >
                <div class="relative h-48 overflow-hidden">
                  <img
                    :src="safari.image"
                    :alt="safari.title"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
                  <div class="absolute left-4 top-4 rounded-full bg-safari-gold/90 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-charcoal">
                    {{ safari.badge }}
                  </div>
                  <p class="absolute inset-x-4 bottom-4 text-sm font-semibold uppercase tracking-[0.35em] text-white/80" v-html="safari.meta"></p>
                </div>
                <div class="flex h-full flex-col gap-4 p-6">
                  <h3 class="text-xl font-heading text-white" v-html="safari.title"></h3>
                  <p class="text-sm leading-relaxed text-white/70">
                    {{ safari.summary }}
                  </p>
                  <ul class="space-y-2 text-sm text-white/70">
                    <li v-for="point in safari.highlights" :key="point" class="flex gap-2">
                      <span aria-hidden="true" class="mt-1 h-1.5 w-1.5 rounded-full bg-safari-gold"></span>
                      <span v-html="point"></span>
                    </li>
                  </ul>
                  <div class="mt-auto flex items-center justify-between pt-4">
                    <button class="text-sm font-semibold uppercase tracking-[0.35em] text-safari-gold transition hover:text-white">
                      Download PDF
                    </button>
                    <a
                      href="#contact"
                      class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 transition hover:text-white"
                    >
                      Tailor This
                      <span aria-hidden="true">→</span>
                    </a>
                  </div>
                </div>
              </article>
            </div>
            <aside class="flex flex-col justify-between rounded-3xl bg-black/30 p-8 backdrop-blur md:sticky md:top-28">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                  How it Works
                </p>
                <h3 class="mt-4 text-3xl font-heading text-white">
                  A four-step blueprint to your bespoke safari
                </h3>
                <ol class="mt-6 space-y-5 text-sm text-white/70">
                  <li class="flex gap-4">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 font-semibold text-white/80">
                      1
                    </span>
                    Discovery call to understand your travel style, dream sightings, and timeframe.
                  </li>
                  <li class="flex gap-4">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 font-semibold text-white/80">
                      2
                    </span>
                    Bespoke itinerary design with multiple accommodation and charter options to compare.
                  </li>
                  <li class="flex gap-4">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 font-semibold text-white/80">
                      3
                    </span>
                    Confirmed arrangements with detailed travel pack, packing lists, and guest portal access.
                  </li>
                  <li class="flex gap-4">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 font-semibold text-white/80">
                      4
                    </span>
                    On-the-ground concierge monitoring flights, weather, and wildlife movements 24/7.
                  </li>
                </ol>
              </div>
              <div class="mt-8 rounded-2xl border border-white/20 bg-white/5 p-6">
                <p class="text-sm uppercase tracking-[0.35em] text-safari-gold">
                  Need inspiration?
                </p>
                <p class="mt-3 text-sm leading-relaxed text-white/80">
                  Ask about our privately hosted expeditions: the Great Ape Odyssey, Selous river cruises, and family-friendly
                  conservation safaris with junior ranger programmes.
                </p>
              </div>
            </aside>
          </div>
        </div>
      </section>

      <section id="destinations" class="relative overflow-hidden bg-white py-20">
        <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-safari-sand/40 to-transparent"></div>
        <div class="relative mx-auto max-w-6xl px-6">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                Iconic Destinations
              </p>
              <h2 class="mt-4 text-4xl font-heading text-charcoal sm:text-5xl">
                Choose the landscapes that speak to your sense of wonder
              </h2>
              <p class="mt-4 max-w-2xl text-sm leading-relaxed text-charcoal/75">
                We orchestrate seamless multi-region journeys that stitch together the north’s wildlife circuits,
                the remote south, and island escapes. Each region unlocks new textures, cultures, and wildlife moments.
              </p>
            </div>
            <a
              href="#lodges"
              class="inline-flex items-center gap-3 self-start rounded-full border border-safari-green px-6 py-3 text-sm font-semibold text-safari-green transition hover:bg-safari-green hover:text-white"
            >
              Explore Our Lodge Collection
              <span aria-hidden="true">→</span>
            </a>
          </div>

          <div class="mt-12 grid gap-6 lg:grid-cols-6">
            <article
              v-for="(spot, index) in destinationSpots"
              :key="spot.name"
              class="group relative overflow-hidden rounded-[32px]"
              :class="[
                index === 0 ? 'lg:col-span-3 lg:row-span-2 min-h-[420px]' : 'lg:col-span-3 min-h-[280px]',
                index > 0 ? 'bg-safari-sand/20' : '',
              ]"
            >
              <img
                :src="spot.image"
                :alt="spot.name"
                class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/40 to-transparent"></div>
              <div class="absolute inset-x-0 bottom-0 space-y-3 p-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">{{ spot.tag }}</p>
                <h3 class="text-2xl font-heading">{{ spot.name }}</h3>
                <p class="max-w-lg text-sm text-white/75" v-html="spot.description"></p>
                <div class="flex flex-wrap gap-3 pt-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/70">
                  <span class="rounded-full border border-white/30 px-3 py-1">Sample Itinerary</span>
                  <span class="rounded-full border border-white/30 px-3 py-1">Best Season</span>
                </div>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section id="lodges" class="bg-safari-sand/30 py-20">
        <div class="mx-auto max-w-6xl px-6">
          <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                Lodges &amp; Camps
              </p>
              <h2 class="mt-4 text-4xl font-heading text-charcoal sm:text-5xl">
                Hand-selected stays that marry safari romance with elevated comfort
              </h2>
              <p class="mt-4 text-sm leading-relaxed text-charcoal/75">
                From treehouse suites and star-bed sleepouts to oceanfront sanctuaries, we recommend properties that align
                with your style—whether that is contemporary design, authentic bushcraft, or family-friendly amenities.
              </p>
              <ul class="mt-8 space-y-4 text-sm text-charcoal/80">
                <li class="flex gap-3">
                  <span class="mt-1 inline-flex h-2.5 w-2.5 flex-none rounded-full bg-safari-gold"></span>
                  Exclusive-use villas, honeymoon retreats, and multi-generational residences.
                </li>
                <li class="flex gap-3">
                  <span class="mt-1 inline-flex h-2.5 w-2.5 flex-none rounded-full bg-safari-gold"></span>
                  Wellness experiences: bush spas, mindfulness decks, and forest bathing trails.
                </li>
                <li class="flex gap-3">
                  <span class="mt-1 inline-flex h-2.5 w-2.5 flex-none rounded-full bg-safari-gold"></span>
                  Conservation levies and community visits woven into every stay.
                </li>
              </ul>
              <div class="mt-8 flex flex-wrap gap-4">
                <a
                  href="#contact"
                  class="rounded-full bg-safari-green px-6 py-3 text-sm font-semibold text-white transition hover:bg-charcoal"
                >
                  Request Availability
                </a>
                <a
                  href="#contact"
                  class="rounded-full border border-safari-green px-6 py-3 text-sm font-semibold text-safari-green transition hover:bg-safari-green hover:text-white"
                >
                  Build a Stay List
                </a>
              </div>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
              <article
                v-for="lodge in signatureLodges"
                :key="lodge.name"
                class="group overflow-hidden rounded-[32px] border border-safari-sand/60 bg-white/80 shadow-lg shadow-safari-sand/30 transition hover:-translate-y-2"
              >
                <div class="relative h-52 overflow-hidden">
                  <img
                    :src="lodge.image"
                    :alt="lodge.name"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                  <div class="absolute left-4 top-4 rounded-full bg-white/80 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-charcoal">
                    {{ lodge.location }}
                  </div>
                </div>
                <div class="flex h-full flex-col gap-4 p-6">
                  <h3 class="text-xl font-heading text-charcoal">{{ lodge.name }}</h3>
                  <p class="text-sm text-charcoal/70" v-html="lodge.mood"></p>
                  <div class="mt-auto flex items-center justify-between pt-4 text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                    <span>Inquire</span>
                    <span aria-hidden="true">→</span>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section id="contact" class="relative overflow-hidden bg-charcoal text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(255,255,255,0.05),_transparent_60%)]"></div>
        <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-[url('/images/safari/kelvin-zyteng-gxS48JsmH_0-unsplash.jpg')] bg-cover bg-center opacity-20 lg:block"></div>
        <div class="relative mx-auto max-w-6xl px-6 py-20">
          <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-8">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-safari-gold">
                  Start Planning
                </p>
                <h2 class="mt-4 text-4xl font-heading text-white sm:text-5xl">
                  Share your dream safari—we’ll craft a tailored itinerary within 24 hours
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-white/75">
                  Tell us who you are travelling with, the wildlife you are eager to witness, and your ideal travel window.
                  We will reply with curated route ideas, accommodations, and investment options.
                </p>
              </div>
              <div class="grid gap-4 sm:grid-cols-2">
                <div
                  v-for="channel in contactChannels"
                  :key="channel.label"
                  class="rounded-3xl border border-white/15 bg-white/5 p-6 transition hover:border-safari-gold/60"
                >
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold">{{ channel.label }}</p>
                  <p class="mt-2 text-lg font-heading text-white">{{ channel.value }}</p>
                  <p class="mt-2 text-xs uppercase tracking-[0.25em] text-white/60" v-html="channel.detail"></p>
                </div>
              </div>
              <ul class="space-y-3 text-sm text-white/70">
                <li
                  v-for="fact in contactQuickFacts"
                  :key="fact"
                  class="flex gap-3"
                >
                  <span class="mt-1 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold"></span>
                  <span>{{ fact }}</span>
                </li>
              </ul>
            </div>
            <div class="space-y-6">
              <form class="space-y-5 rounded-3xl border border-white/10 bg-white/10 p-8 backdrop-blur">
                <div class="grid gap-5 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="fullname">
                      Full Name
                    </label>
                    <input
                      id="fullname"
                      type="text"
                      placeholder="Your full name"
                      class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="email">
                      Email Address
                    </label>
                    <input
                      id="email"
                      type="email"
                      placeholder="you@example.com"
                      class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                    />
                  </div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="phone">
                      Phone / WhatsApp
                    </label>
                    <input
                      id="phone"
                      type="tel"
                      placeholder="+255..."
                      class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="travelers">
                      Travellers
                    </label>
                    <input
                      id="travelers"
                      type="number"
                      min="1"
                      placeholder="2 adults, 2 kids..."
                      class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                    />
                  </div>
                </div>
                <div>
                  <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="service">
                    Experience Type
                  </label>
                  <select
                    id="service"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                  >
                    <option class="text-charcoal" value="">Select</option>
                    <option class="text-charcoal" value="wildlife">Wildlife Safari</option>
                    <option class="text-charcoal" value="kili">Kilimanjaro Expedition</option>
                    <option class="text-charcoal" value="coast">Coastal Retreat</option>
                    <option class="text-charcoal" value="honeymoon">Honeymoon Celebration</option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="message">
                    Tell Us More
                  </label>
                  <textarea
                    id="message"
                    rows="4"
                    placeholder="Preferred travel dates, bucket-list sightings, special celebrations..."
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-white placeholder:text-white/40 focus:border-safari-gold focus:outline-none focus:ring-2 focus:ring-safari-gold/40"
                  ></textarea>
                </div>
                <button
                  type="submit"
                  class="w-full rounded-full bg-safari-gold px-6 py-3 text-sm font-semibold text-charcoal transition hover:bg-white"
                >
                  Submit Inquiry
                </button>
              </form>
              <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold">
                  Meet us in person
                </p>
                <p class="mt-3 text-sm text-white/80">
                  Go Tanzania Safari Studio · Sokoine Road, Arusha 23100 · Visits by appointment only
                </p>
                <div class="mt-4 h-40 overflow-hidden rounded-2xl border border-white/10">
                  <iframe
                    title="Go Tanzania Safari Studio Map"
                    src="https://maps.google.com/maps?q=Arusha%2C%20Tanzania&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    class="h-full w-full opacity-80 grayscale"
                    loading="lazy"
                  ></iframe>
                </div>
              </div>
            </div>
          </div>
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
