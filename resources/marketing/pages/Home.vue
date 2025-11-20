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

const heroSlides = ref<HeroSlide[]>([]);
const isLoading = ref(true);
const showNavbar = ref(true);
const lastScrollY = ref(0);

// Fetch hero slides and feature cards from API
onMounted(async () => {
  // Fetch hero slides
  try {
    const response = await fetch('/api/hero-slides');
    const data = await response.json();
    
    // Handle both wrapped and unwrapped responses
    const slides = Array.isArray(data) ? data : (data.data || []);
    
    if (slides.length > 0) {
      heroSlides.value = slides.map((slide: any) => ({
        image: slide.image || '/images/safari/wildlife-savannah.jpg',
        label: slide.label || '',
        title: slide.title || '',
        description: slide.description || '',
        ctaLabel: slide.ctaLabel || 'Learn More',
        ctaHref: slide.ctaHref || '#',
      }));
    }
    
    // Fallback to default slides if API returns empty
    if (heroSlides.value.length === 0) {
      heroSlides.value = [
        {
          image: '/images/safari/moses-londo-5Lm1A5vxczc-unsplash.jpg',
          label: 'Tailored Luxury Adventures',
          title: 'Encounter Tanzania\'s wild soul – from savannah to spice isles',
          description:
            'Join expert guides for once-in-a-lifetime safaris blending iconic wildlife moments, Kilimanjaro summits, and barefoot beach retreats.',
          ctaLabel: 'Explore Signature Safaris',
          ctaHref: '#safaris',
        },
      ];
    }
  } catch (error) {
    console.error('Error fetching hero slides:', error);
    // Fallback to default slide
    heroSlides.value = [
      {
        image: '/images/safari/wildlife-savannah.jpg',
        label: 'Welcome to Tanzania',
        title: 'Discover the beauty of Tanzania',
        description: 'Experience unforgettable safaris and adventures.',
        ctaLabel: 'Explore',
        ctaHref: '#safaris',
      },
    ];
  } finally {
    isLoading.value = false;
    if (heroSlides.value.length > 0) {
      startAutoSlide();
    }
  }

  // Fetch feature cards
  try {
    const response = await fetch('/api/feature-cards');
    const data = await response.json();
    
    // Handle both wrapped and unwrapped responses
    const cards = Array.isArray(data) ? data : (data.data || []);
    
    if (cards.length > 0) {
      featureCards.value = cards.map((card: any) => ({
        icon: card.icon || 'support',
        title: card.title || '',
        headline: card.headline || null,
        copy: card.copy || '',
        count_value: card.count_value || null,
      }));
    }
    
    // Fallback to default cards if API returns empty
    if (featureCards.value.length === 0) {
      featureCards.value = [
        {
          icon: 'travellers',
          title: 'Happy Travellers Yearly',
          headline: '500+ Happy Travellers Yearly',
          copy:
            'Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.',
          count_value: 500,
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
      ];
    }
  } catch (error) {
    console.error('Error fetching feature cards:', error);
    // Fallback to default cards
    featureCards.value = [
      {
        icon: 'travellers',
        title: 'Happy Travellers Yearly',
        headline: '500+ Happy Travellers Yearly',
        copy:
          'Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.',
        count_value: 500,
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
    ];
  }

  // Fetch about stats
  try {
    const response = await fetch('/api/about-stats');
    const data = await response.json();
    
    const stats = Array.isArray(data) ? data : (data.data || []);
    
    if (stats.length > 0) {
      aboutStats.value = stats.map((stat: any) => ({
        value: stat.value || '',
        label: stat.label || '',
      }));
    }
    
    // Fallback to default stats if API returns empty
    if (aboutStats.value.length === 0) {
      aboutStats.value = [
        { value: '18+', label: 'Years curating luxury safaris' },
        { value: '32', label: 'Handpicked camps &amp; lodges' },
        { value: '96%', label: 'Guest satisfaction rating' },
      ];
    }
  } catch (error) {
    console.error('Error fetching about stats:', error);
    // Fallback to default stats
    aboutStats.value = [
      { value: '18+', label: 'Years curating luxury safaris' },
      { value: '32', label: 'Handpicked camps &amp; lodges' },
      { value: '96%', label: 'Guest satisfaction rating' },
    ];
  }

  // Fetch about highlights
  try {
    const response = await fetch('/api/about-highlights');
    const data = await response.json();
    
    const highlights = Array.isArray(data) ? data : (data.data || []);
    
    if (highlights.length > 0) {
      aboutHighlights.value = highlights.map((highlight: any) => ({
        title: highlight.title || '',
        copy: highlight.copy || '',
      }));
    }
    
    // Fallback to default highlights if API returns empty
    if (aboutHighlights.value.length === 0) {
      aboutHighlights.value = [
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
    }
  } catch (error) {
    console.error('Error fetching about highlights:', error);
    // Fallback to default highlights
    aboutHighlights.value = [
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
  }

  // Setup feature observer
  featureObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        featureVisible.value = true;
        const travellersCard = featureCards.value.find(card => card.icon === 'travellers');
        const targetCount = travellersCard?.count_value || TARGET_TRAVELLERS;
        travellerCount.value = 0;
        animateTravellerCount(targetCount);
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

  // Fetch safari packages (itineraries)
  isLoadingSafaris.value = true;
  try {
    const response = await fetch('/api/itineraries?per_page=6');
    if (!response.ok) {
      console.error('API Error:', response.status, response.statusText);
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    
    // Handle paginated response - Laravel returns { data: [...], links: {...}, meta: {...} }
    const itineraries = Array.isArray(data) ? data : (data.data || []);
    
    if (itineraries && itineraries.length > 0) {
      safariPackages.value = itineraries.map((itinerary: any) => ({
        id: itinerary.id,
        slug: itinerary.slug,
        title: itinerary.title || '',
        summary: itinerary.summary || '',
        meta: itinerary.meta || `${itinerary.duration_days || ''} days`,
        image: itinerary.image || itinerary.hero_image?.url || '/images/safari/wildlife-savannah.jpg',
        badge: itinerary.badge || 'Signature Collection',
        highlights: Array.isArray(itinerary.highlights) ? itinerary.highlights : [],
        duration_days: itinerary.duration_days || null,
        price_from: itinerary.price_from || null,
        difficulty: itinerary.difficulty || null,
        service_type: itinerary.service_type?.name || null,
        destination: itinerary.destination?.name || null,
      }));
    }
    
    // Only use fallback if we truly have no data from API
    if (safariPackages.value.length === 0) {
      safariPackages.value = [
        {
          title: 'Great Migration Serengeti Safari',
          summary:
            'Follow the famed wildebeest march with private 4x4 game drives, exclusive viewing decks, and optional sunrise balloon flights.',
          meta: '7 days · Serengeti & Grumeti',
          image: '/images/safari/wildlife-savannah.jpg',
          badge: 'Signature Collection',
          highlights: [
            'Luxury tented camps positioned on migration corridors',
            'Dedicated photographic guide & spotter team',
            'Champagne brunch set on the savannah rim',
          ],
        },
        {
          title: 'Kilimanjaro Machame Summit Trek',
          summary:
            'Pole pole ascent that blends private acclimatisation camps, chef-prepared cuisine, and summit-day oxygen support.',
          meta: '8 days · Machame Route',
          image: '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
          badge: 'Expedition Grade',
          highlights: [
            'Maximum 8 climbers per departure',
            'Hyperbaric chamber & medical lead on trek',
            'Celebratory stay at a boutique Arusha manor',
          ],
        },
        {
          title: 'Zanzibar Spice & Beach Escape',
          summary:
            'Taste Stone Town\'s spice heritage then drift between private villa resorts, dhow cruises, and reef snorkelling.',
          meta: '6 days · Zanzibar Archipelago',
          image: '/images/safari/beach-1.jpg',
          badge: 'Coastal Indulgence',
          highlights: [
            'Guided Stone Town heritage & culinary tour',
            'Private sunset dhow with live taarab music',
            'Wellness rituals at oceanside spa pavilions',
          ],
        },
      ];
    }
  } catch (error) {
    console.error('Error fetching safari packages:', error);
    // Only use fallback on actual error, not if API returned empty array
    if (safariPackages.value.length === 0) {
      safariPackages.value = [
        {
          title: 'Great Migration Serengeti Safari',
        summary:
          'Follow the famed wildebeest march with private 4x4 game drives, exclusive viewing decks, and optional sunrise balloon flights.',
        meta: '7 days · Serengeti & Grumeti',
        image: '/images/safari/wildlife-savannah.jpg',
        badge: 'Signature Collection',
        highlights: [
          'Luxury tented camps positioned on migration corridors',
          'Dedicated photographic guide & spotter team',
          'Champagne brunch set on the savannah rim',
        ],
      },
      {
        title: 'Kilimanjaro Machame Summit Trek',
        summary:
          'Pole pole ascent that blends private acclimatisation camps, chef-prepared cuisine, and summit-day oxygen support.',
        meta: '8 days · Machame Route',
        image: '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
        badge: 'Expedition Grade',
        highlights: [
          'Maximum 8 climbers per departure',
          'Hyperbaric chamber & medical lead on trek',
          'Celebratory stay at a boutique Arusha manor',
        ],
      },
      {
        title: 'Zanzibar Spice & Beach Escape',
        summary:
          'Taste Stone Town\'s spice heritage then drift between private villa resorts, dhow cruises, and reef snorkelling.',
        meta: '6 days · Zanzibar Archipelago',
        image: '/images/safari/beach-1.jpg',
        badge: 'Coastal Indulgence',
        highlights: [
          'Guided Stone Town heritage & culinary tour',
          'Private sunset dhow with live taarab music',
          'Wellness rituals at oceanside spa pavilions',
        ],
      },
    ];
    }
  } finally {
    isLoadingSafaris.value = false;
  }

  // Fetch destinations
  try {
    const response = await fetch('/api/destinations?featured=true&per_page=5');
    if (!response.ok) {
      console.error('API Error:', response.status, response.statusText);
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    console.log('Destinations API Response:', data);
    
    // Handle paginated response - Laravel returns { data: [...], links: {...}, meta: {...} }
    const destinations = Array.isArray(data) ? data : (data.data || []);
    console.log('Extracted destinations:', destinations);
    console.log('Destinations count:', destinations.length);
    
    if (destinations && destinations.length > 0) {
      destinationSpots.value = destinations.map((destination: any) => ({
        name: destination.name || '',
        tag: destination.tag || destination.teaser || '',
        description: destination.description || destination.teaser || '',
        image: destination.image || '/images/safari/wildlife-savannah.jpg',
      }));
      console.log('Mapped destinationSpots:', destinationSpots.value);
    } else {
      console.warn('No destinations found in API response');
    }
    
    // Fallback to default destinations if API returns empty
    if (destinationSpots.value.length === 0) {
      destinationSpots.value = [
        {
          name: 'Serengeti National Park',
          tag: 'Great Migration',
          description: 'River-crossing drama, predator action, endless horizons under technicolour sunsets.',
          image: '/images/safari/wildlife-savannah.jpg',
        },
        {
          name: 'Ngorongoro Crater',
          tag: 'World Heritage',
          description: 'A collapsed caldera teeming with BIG5 sightings, Maasai culture, and mist-draped mornings.',
          image: '/images/safari/wildlife-herd.jpg',
        },
        {
          name: 'Mount Kilimanjaro',
          tag: 'Summit Trek',
          description: 'Africa\'s rooftop crowned by glaciers, alpine desert moonscapes, and iconic Uhuru sunrise.',
          image: '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
        },
        {
          name: 'Ruaha & Selous Reserves',
          tag: 'Southern Circuit',
          description: 'Remote fly-in safaris with boating on the Rufiji, walking trails, and off-grid luxury camps.',
          image: '/images/safari/wildlife-herd.jpg',
        },
        {
          name: 'Zanzibar Archipelago',
          tag: 'Coastal Haven',
          description: 'From Matemwe reefs to Mnemba atoll, drift between spice farms and barefoot luxury hideaways.',
          image: '/images/safari/beach-2.jpg',
        },
      ];
    }
  } catch (error) {
    console.error('Error fetching destinations:', error);
    // Fallback to default destinations
    if (destinationSpots.value.length === 0) {
      destinationSpots.value = [
        {
          name: 'Serengeti National Park',
          tag: 'Great Migration',
          description: 'River-crossing drama, predator action, endless horizons under technicolour sunsets.',
          image: '/images/safari/wildlife-savannah.jpg',
        },
        {
          name: 'Ngorongoro Crater',
          tag: 'World Heritage',
          description: 'A collapsed caldera teeming with BIG5 sightings, Maasai culture, and mist-draped mornings.',
          image: '/images/safari/wildlife-herd.jpg',
        },
        {
          name: 'Mount Kilimanjaro',
          tag: 'Summit Trek',
          description: 'Africa\'s rooftop crowned by glaciers, alpine desert moonscapes, and iconic Uhuru sunrise.',
          image: '/images/safari/pawan-sharma-GDMPFQPjNlA-unsplash.jpg',
        },
      ];
    }
  }

  // Fetch lodges
  try {
    const response = await fetch('/api/lodges');
    const data = await response.json();
    
    const lodges = Array.isArray(data) ? data : (data.data || []);
    
    if (lodges.length > 0) {
      signatureLodges.value = lodges.map((lodge: any) => ({
        name: lodge.name || '',
        location: lodge.location || '',
        image: lodge.image || '/images/safari/lodge-1.jpg',
        mood: lodge.mood || '',
      }));
    }
    
    // Fallback to default lodges if API returns empty
    if (signatureLodges.value.length === 0) {
      signatureLodges.value = [
        {
          name: 'Four Seasons Safari Lodge',
          location: 'Serengeti Plains',
          image: '/images/safari/lodge-1.jpg',
          mood: 'Waterhole-facing infinity pools & spa sanctuaries in the savannah canopy.',
        },
        {
          name: 'Gibb\'s Farm Manor House',
          location: 'Ngorongoro Highlands',
          image: '/images/safari/lodge-2.jpg',
          mood: 'Artist cottages, organic farm-to-table dining, and valley views wrapped in coffee estates.',
        },
        {
          name: 'The Residence Zanzibar',
          location: 'Kizimkazi Peninsula',
          image: '/images/safari/beach-3.jpg',
          mood: 'Private pool villas, butler-led service, and azure lagoons inspired by Swahili heritage.',
        },
        {
          name: 'Chem Chem Lodge',
          location: 'Lake Manyara Corridor',
          image: '/images/safari/alferio-njau-MESNFA-pINg-unsplash.jpg',
          mood: 'Slow safari philosophy with sunrise yoga decks, Maasai-led walks, and flamingo-dusted vistas.',
        },
      ];
    }
  } catch (error) {
    console.error('Error fetching lodges:', error);
    // Fallback to default lodges
    signatureLodges.value = [
      {
        name: 'Four Seasons Safari Lodge',
        location: 'Serengeti Plains',
        image: '/images/safari/lodge-1.jpg',
        mood: 'Waterhole-facing infinity pools & spa sanctuaries in the savannah canopy.',
      },
      {
        name: 'Gibb\'s Farm Manor House',
        location: 'Ngorongoro Highlands',
        image: '/images/safari/lodge-2.jpg',
        mood: 'Artist cottages, organic farm-to-table dining, and valley views wrapped in coffee estates.',
      },
      {
        name: 'The Residence Zanzibar',
        location: 'Kizimkazi Peninsula',
        image: '/images/safari/beach-3.jpg',
        mood: 'Private pool villas, butler-led service, and azure lagoons inspired by Swahili heritage.',
      },
      {
        name: 'Chem Chem Lodge',
        location: 'Lake Manyara Corridor',
        image: '/images/safari/alferio-njau-MESNFA-pINg-unsplash.jpg',
        mood: 'Slow safari philosophy with sunrise yoga decks, Maasai-led walks, and flamingo-dusted vistas.',
      },
    ];
  }
});

const currentSlide = ref(0);
const activeSlide = computed<HeroSlide>(() => {
  if (heroSlides.value.length === 0) {
    return {
      image: '/images/safari/wildlife-savannah.jpg',
      label: '',
      title: '',
      description: '',
      ctaLabel: '',
      ctaHref: '#',
    };
  }
  return heroSlides.value[currentSlide.value] || heroSlides.value[0];
});

let intervalId: ReturnType<typeof setInterval> | undefined;

const startAutoSlide = () => {
  if (heroSlides.value.length === 0) return;
  if (intervalId) {
    clearInterval(intervalId);
  }
  intervalId = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % heroSlides.value.length;
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
  if (heroSlides.value.length === 0) return;
  currentSlide.value = (currentSlide.value + 1) % heroSlides.value.length;
  restartAutoSlide();
};

const prevSlide = () => {
  if (heroSlides.value.length === 0) return;
  currentSlide.value =
    (currentSlide.value - 1 + heroSlides.value.length) % heroSlides.value.length;
  restartAutoSlide();
};


// Navbar scroll behavior
const handleScroll = () => {
  const currentScrollY = window.scrollY;
  
  // Show navbar at top or when scrolling up
  if (currentScrollY < 100 || currentScrollY < lastScrollY.value) {
    showNavbar.value = true;
  } else {
    // Hide navbar when scrolling down past 100px
    showNavbar.value = false;
  }
  
  lastScrollY.value = currentScrollY;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
  
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

type SafariPackage = {
  id?: number;
  slug?: string;
  title: string;
  summary: string;
  meta: string;
  image: string;
  badge: string;
  highlights: string[];
  duration_days?: number | null;
  price_from?: number | string | null;
  difficulty?: string | null;
  service_type?: string | null;
  destination?: string | null;
};

const safariPackages = ref<SafariPackage[]>([]);
const isLoadingSafaris = ref(true);

type DestinationSpot = {
  name: string;
  tag: string;
  description: string;
  image: string;
};

const destinationSpots = ref<DestinationSpot[]>([]);

type Lodge = {
  name: string;
  location: string;
  image: string;
  mood: string;
};

const signatureLodges = ref<Lodge[]>([]);

type AboutStat = {
  value: string;
  label: string;
};

type AboutHighlight = {
  title: string;
  copy: string;
};

const aboutStats = ref<AboutStat[]>([]);
const aboutHighlights = ref<AboutHighlight[]>([]);

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

type FeatureCard = {
  icon: 'travellers' | 'pricing' | 'support';
  title: string;
  headline?: string;
  copy: string;
  count_value?: number;
};

const featureCards = ref<FeatureCard[]>([]);
const featureSectionTitle = ref('Why Travel With Us');
const featureSectionSubtitle = ref('We blend decades of on-the-ground knowledge with bespoke planning to deliver effortless journeys beyond the guidebooks.');

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
  const travellersCard = featureCards.value.find(card => card.icon === 'travellers');
  const targetCount = travellersCard?.count_value || TARGET_TRAVELLERS;
  animateTravellerCount(targetCount + 40, 400);
};

const settleTravellerCount = () => {
  const travellersCard = featureCards.value.find(card => card.icon === 'travellers');
  const targetCount = travellersCard?.count_value || TARGET_TRAVELLERS;
  animateTravellerCount(targetCount, 600);
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
    <header 
      class="fixed top-0 left-0 right-0 z-50 pt-4 pb-4 transition-transform duration-300"
      :class="showNavbar ? 'translate-y-0' : '-translate-y-full'"
    >
      <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 lg:px-8">
        <a class="flex items-center transition-all duration-300 hover:scale-105" href="#home">
          <img
            src="/images/safari/mpya.png"
            alt="Go Tanzania Safari"
            class="h-36 w-auto object-contain lg:h-48"
            loading="eager"
            style="filter: brightness(1.5) contrast(1.6) drop-shadow(0 8px 24px rgba(217, 154, 56, 0.6));"
          />
        </a>
        <div class="hidden items-center gap-3 lg:flex">
          <nav class="flex items-center gap-2">
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#home"
            >
              Home
            </a>
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#about"
            >
              About
            </a>
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#safaris"
            >
              Safaris
            </a>
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#destinations"
            >
              Destinations
            </a>
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#lodges"
            >
              Lodges
            </a>
            <a 
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
              href="#contact"
            >
              Contact
            </a>
          </nav>
          <a
            href="#contact"
            class="rounded-full border-2 border-safari-gold bg-safari-gold/30 backdrop-blur-md px-6 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-safari-gold shadow-glow-gold transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:shadow-glow-gold hover:scale-110"
          >
            Start Planning
          </a>
        </div>
        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-safari-gold/50 bg-black/60 backdrop-blur-md text-safari-gold shadow-medium transition-all duration-300 hover:bg-safari-gold/20 hover:border-safari-gold hover:shadow-glow-gold hover:scale-110 lg:hidden"
          aria-label="Open navigation"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </header>

    <main>
      <section id="home" class="relative">
        <div class="relative h-[85vh] min-h-[600px]">
          <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-gray-900">
            <div class="text-center text-white">
              <div class="mb-4 inline-block h-8 w-8 animate-spin rounded-full border-4 border-white border-t-transparent"></div>
              <p class="text-sm">Loading...</p>
            </div>
          </div>
          <div
            v-else
            v-for="(slide, index) in heroSlides"
            :key="slide.image + index"
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

          <div class="relative z-10 mx-auto flex h-full max-w-6xl flex-col justify-end px-6 pb-20 pt-16 text-white">
            <p class="text-sm font-semibold uppercase tracking-[0.5em] text-safari-gold mb-4 animate-fade-in">
              {{ activeSlide.label }}
            </p>
            <h1 class="mt-2 max-w-4xl text-5xl font-heading font-bold leading-[1.1] tracking-tight sm:text-6xl lg:text-7xl text-balance animate-fade-in-up">
              {{ activeSlide.title }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-white/90 animate-fade-in-up" style="animation-delay: 0.1s">
              {{ activeSlide.description }}
            </p>
            <div class="mt-10 flex flex-wrap gap-4 animate-fade-in-up" style="animation-delay: 0.2s">
              <a
                :href="activeSlide.ctaHref"
                class="group rounded-full bg-safari-gold px-8 py-4 text-sm font-semibold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105"
              >
                {{ activeSlide.ctaLabel }}
                <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
              </a>
              <a
                href="#destinations"
                class="group rounded-full border-2 border-safari-gold/80 bg-safari-gold/10 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-safari-gold transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold"
              >
                View Destinations
                <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
              </a>
            </div>
          </div>

          <div v-if="!isLoading && heroSlides.length > 0" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

          <div v-if="!isLoading && heroSlides.length > 0" class="absolute inset-x-0 bottom-10 z-20 flex items-center justify-between px-6">
            <div class="flex gap-4">
              <button
                type="button"
                class="pointer-events-auto rounded-full border-2 border-white/50 glass-dark p-4 text-white transition-all duration-300 hover:bg-white/20 hover:border-safari-gold hover:scale-110 hover:shadow-glow-gold"
                @click="prevSlide"
                aria-label="Previous slide"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
              </button>
              <button
                type="button"
                class="pointer-events-auto rounded-full border-2 border-white/50 glass-dark p-4 text-white transition-all duration-300 hover:bg-white/20 hover:border-safari-gold hover:scale-110 hover:shadow-glow-gold"
                @click="nextSlide"
                aria-label="Next slide"
              >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5" />
                </svg>
              </button>
            </div>
            <div class="pointer-events-auto flex gap-2.5">
              <button
                v-for="(slide, index) in heroSlides"
                :key="slide.image + index"
                type="button"
                class="h-2 w-10 rounded-full transition-all duration-300"
                :class="currentSlide === index ? 'bg-safari-gold shadow-glow-gold' : 'bg-white/40 hover:bg-white/70 hover:w-12'"
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
          'relative overflow-hidden bg-gradient-to-b from-safari-sand/30 via-white to-safari-sand/20 py-24 transition-all duration-700 ease-out',
          featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10',
        ]"
      >
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(217,154,56,0.08),transparent_50%)]"></div>
        <div class="relative mx-auto max-w-5xl px-6 text-center">
          <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold mb-4">
            Our Promise
          </p>
          <h2 class="mt-3 text-4xl font-heading font-bold text-charcoal sm:text-5xl lg:text-6xl text-balance">
            {{ featureSectionTitle }}
          </h2>
          <p class="mt-6 text-lg leading-relaxed text-charcoal/75 max-w-2xl mx-auto">
            {{ featureSectionSubtitle }}
          </p>
        </div>
        <div class="relative mx-auto mt-16 grid max-w-7xl gap-8 px-6 md:grid-cols-3">
          <article
            v-for="(feature, index) in featureCards"
            :key="feature.title"
            class="group relative rounded-3xl border border-safari-sand/50 bg-white/90 backdrop-blur-sm p-10 text-center transition-all duration-500 ease-out shadow-soft hover:-translate-y-2 hover:shadow-large hover:border-safari-gold/40"
            :class="featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
            :style="{ transitionDelay: featureVisible ? `${index * 120}ms` : '0ms' }"
            @mouseenter="feature.icon === 'travellers' && boostTravellerCount()"
            @mouseleave="feature.icon === 'travellers' && settleTravellerCount()"
          >
            <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-safari-gold/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
            <div class="relative mx-auto flex h-24 w-24 items-center justify-center rounded-2xl border-2 border-safari-gold/60 bg-gradient-to-br from-safari-gold/10 to-transparent text-safari-gold shadow-medium transition-all duration-300 group-hover:scale-110 group-hover:border-safari-gold group-hover:shadow-glow-gold">
              <component :is="getFeatureIcon(feature.icon)" />
            </div>
            <template v-if="feature.icon === 'travellers'">
              <p class="mt-8 text-5xl font-heading font-bold text-safari-green">
                {{ travellerCount.toLocaleString() }}
                <span class="text-3xl font-bold text-safari-gold">+</span>
              </p>
              <h3 class="mt-3 text-xl font-semibold tracking-tight text-charcoal">
                {{ feature.title }}
              </h3>
            </template>
            <template v-else>
              <h3 class="mt-8 text-xl font-semibold tracking-tight text-charcoal">
                {{ feature.title }}
              </h3>
            </template>
            <div class="mx-auto mt-4 h-0.5 w-16 bg-gradient-to-r from-transparent via-safari-gold to-transparent"></div>
            <p class="mt-6 text-base leading-relaxed text-charcoal/75">
              {{ feature.copy }}
            </p>
          </article>
        </div>
      </section>

      <section id="about" class="relative overflow-hidden bg-gradient-to-b from-white via-safari-sand/10 to-white py-28">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(217,154,56,0.06),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,rgba(31,59,43,0.04),transparent_50%)]"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="grid gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div class="space-y-6">
              <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                Authentic Tanzanian Expertise
              </p>
              <h2 class="text-4xl font-heading font-bold text-charcoal leading-tight sm:text-5xl lg:text-6xl text-balance">
                Journeys designed by locals who live, breathe, and protect Tanzania
              </h2>
              <p class="text-lg leading-relaxed text-charcoal/75 max-w-2xl">
                We move beyond brochure itineraries. Our Dar es Salaam and Arusha teams collaborate daily
                with guides, lodge owners, and conservation partners to secure privileged access and real-time
                intelligence. The result: safaris that feel effortless, immersive, and entirely your own.
              </p>
              <div class="flex flex-wrap gap-4 pt-4">
                <a
                  href="#contact"
                  class="group rounded-full bg-safari-green px-8 py-4 text-sm font-semibold text-white transition-all duration-300 hover:bg-charcoal hover:shadow-glow-green hover:scale-105"
                >
                  Speak to a Journey Architect
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
                <a
                  href="#safaris"
                  class="group rounded-full border-2 border-safari-green bg-white/50 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-safari-green transition-all duration-300 hover:bg-safari-green hover:text-white hover:shadow-medium"
                >
                  View Sample Safaris
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
              </div>
            </div>
            <div class="rounded-3xl glass border-safari-sand/30 p-10 shadow-large">
              <ul class="grid gap-6 sm:grid-cols-3 lg:grid-cols-1">
                <li
                  v-for="stat in aboutStats"
                  :key="stat.label"
                  class="group rounded-2xl bg-gradient-to-br from-safari-sand/40 to-safari-sand/20 px-8 py-6 text-center transition-all duration-300 hover:shadow-medium hover:scale-105"
                >
                  <p class="text-4xl font-heading font-bold text-safari-green">{{ stat.value }}</p>
                  <p class="mt-3 text-xs font-semibold uppercase tracking-[0.25em] text-charcoal/70" v-html="stat.label"></p>
                </li>
              </ul>
              <div class="mt-10 rounded-2xl border border-safari-gold/20 bg-gradient-to-br from-safari-gold/5 to-transparent p-8">
                <p class="text-sm font-bold uppercase tracking-[0.35em] text-safari-gold">Accreditations</p>
                <p class="mt-4 text-sm leading-relaxed text-charcoal/80">
                  Proud members of the Tanzania Association of Tour Operators (TATO), ATTA, and Leave No Trace. We
                  hand-select suppliers championing eco-conscious luxury.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-20 grid gap-8 md:grid-cols-3">
            <article
              v-for="highlight in aboutHighlights"
              :key="highlight.title"
              class="group relative rounded-3xl border border-safari-sand/40 glass p-8 transition-all duration-500 hover:-translate-y-3 hover:border-safari-gold/60 hover:shadow-large"
            >
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-safari-gold/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
              <div class="relative inline-flex rounded-full bg-gradient-to-r from-safari-gold/10 to-safari-gold/5 border border-safari-gold/20 px-5 py-2 text-xs font-bold uppercase tracking-[0.3em] text-safari-green">
                Core Pillar
              </div>
              <h3 class="mt-6 text-2xl font-heading font-bold text-charcoal">{{ highlight.title }}</h3>
              <p class="mt-4 text-base leading-relaxed text-charcoal/75">
                {{ highlight.copy }}
              </p>
              <div class="mt-6 flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.3em] text-safari-gold transition-all duration-300 group-hover:gap-3">
                Learn More
                <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">→</span>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section id="safaris" class="relative overflow-hidden bg-gradient-to-b from-safari-green via-safari-green-dark to-safari-green py-32 text-white">
        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10">
          <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.2) 1px, transparent 0); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Subtle overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/40 via-transparent to-black/30"></div>
        
        <div class="relative mx-auto max-w-7xl px-6">
          <!-- Header Section -->
          <div class="mb-20 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold mb-6">
              Signature Safaris
            </p>
            <h2 class="text-5xl font-heading font-bold text-white leading-tight sm:text-6xl lg:text-7xl mb-8 text-balance">
              Preview a few of our most-requested<br />
              <span class="text-safari-gold">tailored itineraries</span>
            </h2>
            <p class="mx-auto max-w-3xl text-xl leading-relaxed text-white/90">
              Every journey is individually reimagined around your pace, interests, and travel style. Use these curated
              blueprints as inspiration—our team refines them into a completely bespoke adventure.
            </p>
          </div>

          <!-- Safari Packages Grid -->
          <div v-if="safariPackages.length > 0" class="grid gap-10 md:grid-cols-2 lg:grid-cols-3 mb-20">
            <article
              v-for="(safari, index) in safariPackages"
              :key="safari.id || safari.title || index"
              class="group relative flex flex-col overflow-hidden rounded-3xl glass-dark border border-white/20 shadow-large transition-all duration-500 hover:-translate-y-4 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.6)] hover:bg-white/15 hover:border-white/30"
            >
              <!-- Image Container -->
              <div class="relative h-64 overflow-hidden">
                <img
                  :src="safari.image"
                  :alt="safari.title"
                  class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                
                <!-- Badge -->
                <div class="absolute left-6 top-6 z-10">
                  <span class="inline-flex items-center rounded-full bg-safari-gold px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-charcoal shadow-glow-gold backdrop-blur-sm">
                    {{ safari.badge || 'Signature' }}
                  </span>
                </div>

                <!-- Meta Info Overlay -->
                <div class="absolute inset-x-6 bottom-6">
                  <div class="flex items-center gap-3 text-sm font-semibold text-white">
                    <span v-if="safari.duration_days" class="flex items-center gap-1.5">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ safari.duration_days }} days
                    </span>
                    <span v-if="safari.difficulty" class="flex items-center gap-1.5">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                      </svg>
                      {{ safari.difficulty }}
                    </span>
                  </div>
                  <p v-if="safari.meta" class="mt-2 text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold/90" v-html="safari.meta"></p>
                </div>
              </div>

              <!-- Content -->
              <div class="flex flex-1 flex-col gap-5 p-8">
                <div>
                  <h3 class="text-2xl font-heading text-white mb-3 leading-tight" v-html="safari.title"></h3>
                  <p v-if="safari.summary" class="text-sm leading-relaxed text-white/75 line-clamp-3">
                    {{ safari.summary }}
                  </p>
                </div>

                <!-- Highlights -->
                <div v-if="safari.highlights && safari.highlights.length > 0" class="space-y-2.5">
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold mb-2">Highlights</p>
                  <ul class="space-y-2 text-sm text-white/70">
                    <li v-for="(point, idx) in safari.highlights.slice(0, 3)" :key="idx" class="flex items-start gap-3">
                      <span aria-hidden="true" class="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full bg-safari-gold"></span>
                      <span class="flex-1" v-html="point"></span>
                    </li>
                  </ul>
                </div>

                <!-- Price & Actions -->
                <div class="mt-auto pt-6 border-t border-white/10">
                  <div v-if="safari.price_from" class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60 mb-1">Starting from</p>
                    <p class="text-2xl font-bold text-safari-gold">
                      ${{ typeof safari.price_from === 'number' ? safari.price_from.toLocaleString() : safari.price_from }}
                    </p>
                  </div>
                  <div class="flex items-center gap-3">
                    <router-link
                      :to="`/itineraries/${safari.slug || ''}`"
                      class="group flex-1 rounded-full bg-safari-gold px-6 py-3.5 text-center text-sm font-semibold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105"
                    >
                      View Details
                      <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                    </router-link>
                    <a
                      href="#contact"
                      class="group flex items-center justify-center rounded-full border-2 border-white/40 bg-white/5 backdrop-blur-sm px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:border-white hover:bg-white/20 hover:scale-105"
                    >
                      Tailor
                      <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                      </svg>
                    </a>
                  </div>
                </div>
              </div>

              <!-- Hover Effect Glow -->
              <div class="absolute inset-0 rounded-[2rem] opacity-0 transition-opacity duration-500 group-hover:opacity-100 pointer-events-none">
                <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-br from-safari-gold/10 via-transparent to-transparent"></div>
              </div>
            </article>
          </div>

          <!-- Loading State -->
          <div v-else-if="isLoadingSafaris" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 mb-16">
            <div v-for="i in 3" :key="i" class="h-[600px] rounded-[2rem] bg-white/5 animate-pulse"></div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-16">
            <p class="text-white/60">No safaris available at the moment. Please check back later.</p>
          </div>

          <!-- CTA Section -->
          <div class="mt-20 text-center">
            <a
              href="#contact"
              class="group inline-flex items-center gap-3 rounded-full bg-safari-gold px-10 py-5 text-base font-bold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105"
            >
              Plan Your Custom Safari
              <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
            </a>
          </div>
        </div>
      </section>

      <section id="destinations" class="relative overflow-hidden bg-gradient-to-b from-white via-safari-sand/10 to-white py-28">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(217,154,56,0.05),transparent_50%)]"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between mb-16">
            <div class="space-y-4">
              <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                Iconic Destinations
              </p>
              <h2 class="text-4xl font-heading font-bold text-charcoal leading-tight sm:text-5xl lg:text-6xl text-balance">
                Choose the landscapes that speak to your sense of wonder
              </h2>
              <p class="max-w-2xl text-lg leading-relaxed text-charcoal/75">
                We orchestrate seamless multi-region journeys that stitch together the north's wildlife circuits,
                the remote south, and island escapes. Each region unlocks new textures, cultures, and wildlife moments.
              </p>
            </div>
            <a
              href="#lodges"
              class="group inline-flex items-center gap-3 self-start rounded-full border-2 border-safari-green bg-white/50 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-safari-green transition-all duration-300 hover:bg-safari-green hover:text-white hover:shadow-medium hover:scale-105"
            >
              Explore Our Lodge Collection
              <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">→</span>
            </a>
          </div>

          <div v-if="destinationSpots.length > 0" class="grid gap-8 lg:grid-cols-6">
            <article
              v-for="(spot, index) in destinationSpots"
              :key="spot.name || index"
              class="group relative overflow-hidden rounded-3xl shadow-large transition-all duration-500 hover:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-2"
              :class="[
                index === 0 ? 'lg:col-span-3 lg:row-span-2 min-h-[480px]' : 'lg:col-span-3 min-h-[320px]',
              ]"
            >
              <img
                :src="spot.image"
                :alt="spot.name"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/50 to-black/30"></div>
              <div class="absolute inset-x-0 bottom-0 space-y-4 p-8 text-white">
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-safari-gold">{{ spot.tag }}</p>
                <h3 class="text-3xl font-heading font-bold leading-tight">{{ spot.name }}</h3>
                <p class="max-w-lg text-base leading-relaxed text-white/90" v-html="spot.description"></p>
                <div class="flex flex-wrap gap-3 pt-4">
                  <span class="rounded-full border border-white/40 bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-white/90">Sample Itinerary</span>
                  <span class="rounded-full border border-white/40 bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-white/90">Best Season</span>
                </div>
              </div>
            </article>
          </div>
          <div v-else class="mt-12 text-center py-12">
            <p class="text-charcoal/60">Loading destinations...</p>
          </div>
        </div>
      </section>

      <section id="lodges" class="relative overflow-hidden bg-gradient-to-b from-safari-sand/20 via-white to-safari-sand/10 py-28">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,rgba(31,59,43,0.04),transparent_50%)]"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="grid gap-16 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div class="space-y-8">
              <div class="space-y-6">
                <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                  Lodges &amp; Camps
                </p>
                <h2 class="text-4xl font-heading font-bold text-charcoal leading-tight sm:text-5xl lg:text-6xl text-balance">
                  Hand-selected stays that marry safari romance with elevated comfort
                </h2>
                <p class="text-lg leading-relaxed text-charcoal/75">
                  From treehouse suites and star-bed sleepouts to oceanfront sanctuaries, we recommend properties that align
                  with your style—whether that is contemporary design, authentic bushcraft, or family-friendly amenities.
                </p>
              </div>
              <ul class="space-y-5 text-base text-charcoal/80">
                <li class="flex gap-4">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold"></span>
                  <span>Exclusive-use villas, honeymoon retreats, and multi-generational residences.</span>
                </li>
                <li class="flex gap-4">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold"></span>
                  <span>Wellness experiences: bush spas, mindfulness decks, and forest bathing trails.</span>
                </li>
                <li class="flex gap-4">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold"></span>
                  <span>Conservation levies and community visits woven into every stay.</span>
                </li>
              </ul>
              <div class="flex flex-wrap gap-4 pt-4">
                <a
                  href="#contact"
                  class="group rounded-full bg-safari-green px-8 py-4 text-sm font-semibold text-white transition-all duration-300 hover:bg-charcoal hover:shadow-glow-green hover:scale-105"
                >
                  Request Availability
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
                <a
                  href="#contact"
                  class="group rounded-full border-2 border-safari-green bg-white/50 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-safari-green transition-all duration-300 hover:bg-safari-green hover:text-white hover:shadow-medium"
                >
                  Build a Stay List
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
              </div>
            </div>
            <div class="grid gap-8 sm:grid-cols-2">
              <article
                v-for="lodge in signatureLodges"
                :key="lodge.name"
                class="group relative overflow-hidden rounded-3xl glass border border-safari-sand/40 shadow-medium transition-all duration-500 hover:-translate-y-3 hover:shadow-large hover:border-safari-gold/40"
              >
                <div class="relative h-64 overflow-hidden">
                  <img
                    :src="lodge.image"
                    :alt="lodge.name"
                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                  <div class="absolute left-6 top-6 rounded-full glass border border-white/30 px-5 py-2 text-xs font-bold uppercase tracking-[0.3em] text-charcoal shadow-soft">
                    {{ lodge.location }}
                  </div>
                </div>
                <div class="flex h-full flex-col gap-5 p-8">
                  <h3 class="text-2xl font-heading font-bold text-charcoal">{{ lodge.name }}</h3>
                  <p class="text-base leading-relaxed text-charcoal/75" v-html="lodge.mood"></p>
                  <div class="mt-auto flex items-center justify-between pt-6 border-t border-safari-sand/40 text-sm font-bold uppercase tracking-[0.3em] text-safari-gold transition-all duration-300 group-hover:gap-4">
                    <span>Inquire</span>
                    <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section id="contact" class="relative overflow-hidden bg-gradient-to-b from-charcoal via-charcoal-dark to-charcoal text-white py-32">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(217,154,56,0.08),transparent_70%)]"></div>
        <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-[url('/images/safari/kelvin-zyteng-gxS48JsmH_0-unsplash.jpg')] bg-cover bg-center opacity-10 lg:block"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="grid gap-16 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-10">
              <div class="space-y-6">
                <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                  Start Planning
                </p>
                <h2 class="text-4xl font-heading font-bold text-white leading-tight sm:text-5xl lg:text-6xl text-balance">
                  Share your dream safari—we'll craft a tailored itinerary within 24 hours
                </h2>
                <p class="text-lg leading-relaxed text-white/80">
                  Tell us who you are travelling with, the wildlife you are eager to witness, and your ideal travel window.
                  We will reply with curated route ideas, accommodations, and investment options.
                </p>
              </div>
              <div class="grid gap-6 sm:grid-cols-2">
                <div
                  v-for="channel in contactChannels"
                  :key="channel.label"
                  class="group rounded-3xl border border-white/20 glass-dark p-8 transition-all duration-300 hover:border-safari-gold/60 hover:bg-white/10 hover:shadow-medium"
                >
                  <p class="text-xs font-bold uppercase tracking-[0.35em] text-safari-gold mb-3">{{ channel.label }}</p>
                  <p class="text-xl font-heading font-bold text-white">{{ channel.value }}</p>
                  <p class="mt-3 text-sm uppercase tracking-[0.2em] text-white/70" v-html="channel.detail"></p>
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
            <div class="space-y-8">
              <form class="space-y-6 rounded-3xl border border-white/20 glass-dark p-10 shadow-large">
                <div class="grid gap-5 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="fullname">
                      Full Name
                    </label>
                    <input
                      id="fullname"
                      type="text"
                      placeholder="Your full name"
                      class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
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
                      class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
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
                      class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
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
                      class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
                    />
                  </div>
                </div>
                <div>
                  <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="service">
                    Experience Type
                  </label>
                  <select
                    id="service"
                    class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
                  >
                    <option class="text-charcoal bg-white" value="">Select</option>
                    <option class="text-charcoal bg-white" value="wildlife">Wildlife Safari</option>
                    <option class="text-charcoal bg-white" value="kili">Kilimanjaro Expedition</option>
                    <option class="text-charcoal bg-white" value="coast">Coastal Retreat</option>
                    <option class="text-charcoal bg-white" value="honeymoon">Honeymoon Celebration</option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="message">
                    Tell Us More
                  </label>
                  <textarea
                    id="message"
                    rows="5"
                    placeholder="Preferred travel dates, bucket-list sightings, special celebrations..."
                    class="mt-2 w-full rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-5 py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50"
                  ></textarea>
                </div>
                <button
                  type="submit"
                  class="group w-full rounded-full bg-safari-gold px-8 py-4 text-sm font-bold text-charcoal transition-all duration-300 hover:bg-safari-gold-light hover:shadow-glow-gold hover:scale-105"
                >
                  Submit Inquiry
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </button>
              </form>
              <div class="rounded-3xl border border-white/20 glass-dark p-8 shadow-medium">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-safari-gold mb-4">
                  Meet us in person
                </p>
                <p class="text-base leading-relaxed text-white/90">
                  Go Tanzania Safari Studio · Sokoine Road, Arusha 23100 · Visits by appointment only
                </p>
                <div class="mt-6 h-48 overflow-hidden rounded-2xl border border-white/20 shadow-soft">
                  <iframe
                    title="Go Tanzania Safari Studio Map"
                    src="https://maps.google.com/maps?q=Arusha%2C%20Tanzania&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    class="h-full w-full opacity-90 grayscale transition-opacity duration-300 hover:opacity-100 hover:grayscale-0"
                    loading="lazy"
                  ></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="relative bg-gradient-to-b from-white to-safari-sand/20 border-t border-safari-sand/30">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(217,154,56,0.03),transparent_50%)]"></div>
      <div class="relative mx-auto max-w-7xl px-6 py-12">
        <div class="flex flex-col gap-6 border-t border-safari-sand/40 pt-8 md:flex-row md:items-center md:justify-between">
          <p class="text-base text-charcoal/70">© {{ new Date().getFullYear() }} Go Tanzania Safari Ltd. All rights reserved.</p>
          <div class="flex gap-6">
            <a class="text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="Instagram">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a class="text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="Facebook">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a class="text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="TripAdvisor">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
