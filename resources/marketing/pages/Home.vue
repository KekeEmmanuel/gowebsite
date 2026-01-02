<script setup lang="ts">
import { computed, h, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  DEFAULT_DESTINATIONS,
  DEFAULT_LODGES,
  DEFAULT_FEATURE_CARDS,
  DEFAULT_HERO_SLIDES,
  fetchWithTimeout,
} from '../data/defaults';

const router = useRouter();

type HeroSlide = {
  image: string;
  label: string;
  title: string;
  description: string;
  ctaLabel: string;
  ctaHref: string;
};

// Initialize with default images immediately (no loading state)
const heroSlides = ref<HeroSlide[]>([...DEFAULT_HERO_SLIDES]);
const showNavbar = ref(true);
const lastScrollY = ref(0);
const showBackToTop = ref(false);

// Fetch hero slides and feature cards from API (background update)
onMounted(async () => {
  // Start auto-slide immediately with default images
  if (heroSlides.value.length > 0) {
    startAutoSlide();
  }

  // Fetch hero slides with timeout and fallback (updates in background)
  try {
    const response = await fetchWithTimeout('/api/hero-slides', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    
    // Handle both wrapped and unwrapped responses
    const slides = Array.isArray(data) ? data : (data.data || []);
    
    if (slides.length > 0) {
      heroSlides.value = slides.map((slide: any) => ({
        image: slide.image || '/images/safari/hero-1.jpg',
        label: slide.label || '',
        title: slide.title || '',
        description: slide.description || '',
        ctaLabel: slide.ctaLabel || 'Learn More',
        ctaHref: slide.ctaHref || '#',
      }));
      // Restart auto-slide with new slides
      if (heroSlides.value.length > 0) {
        restartAutoSlide();
      }
    }
  } catch (error) {
    console.error('Error fetching hero slides (network/timeout/error):', error);
    // Keep using defaults - already set initially
  }

  // Fetch feature cards with timeout and fallback
  try {
    const response = await fetchWithTimeout('/api/feature-cards', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
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
    } else {
      console.warn('No feature cards found in API response, using defaults');
      featureCards.value = [...DEFAULT_FEATURE_CARDS];
    }
  } catch (error) {
    console.error('Error fetching feature cards (network/timeout/error):', error);
    // Always use defaults on any error
    featureCards.value = [...DEFAULT_FEATURE_CARDS];
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

  // Removed safari packages fetching - using tour packages instead

  // Fetch destinations with timeout and fallback
  try {
    const response = await fetchWithTimeout('/api/destinations?featured=true&per_page=5', {}, 8000);
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
        image: destination.image || '/images/safari/wildlife-herd.jpg',
      }));
      console.log('Mapped destinationSpots:', destinationSpots.value);
    } else {
      console.warn('No destinations found in API response, using defaults');
      destinationSpots.value = [...DEFAULT_DESTINATIONS];
    }
  } catch (error) {
    console.error('Error fetching destinations (network/timeout/error):', error);
    // Always use defaults on any error (network, timeout, API error, etc.)
    destinationSpots.value = [...DEFAULT_DESTINATIONS];
  }

  // Fetch lodges with timeout and fallback
  try {
    const response = await fetchWithTimeout('/api/lodges', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    
    const lodges = Array.isArray(data) ? data : (data.data || []);
    
    if (lodges.length > 0) {
      signatureLodges.value = lodges.map((lodge: any) => ({
        name: lodge.name || '',
        location: lodge.location || '',
        image: lodge.hero_image?.url || lodge.image || '/images/safari/beach-1.jpg',
        mood: lodge.mood || '',
        type: lodge.type || 'lodge',
        short_description: lodge.short_description || '',
        amenities: lodge.amenities || [],
        price_from: lodge.price_from || null,
      }));
    } else {
      console.warn('No lodges found in API response, using defaults');
      signatureLodges.value = [...DEFAULT_LODGES];
    }
  } catch (error) {
    console.error('Error fetching lodges (network/timeout/error):', error);
    // Always use defaults on any error
    signatureLodges.value = [...DEFAULT_LODGES];
  }

  // Fetch tour packages with timeout and fallback (show 6 featured packages on homepage)
  isLoadingTourPackages.value = true;
  try {
    const response = await fetchWithTimeout('/api/tour-packages?per_page=6', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    
    // Handle paginated response - Laravel pagination returns { data: [...], links: {...}, meta: {...} }
    // Also handle direct array response
    let packages: any[] = [];
    if (Array.isArray(data)) {
      packages = data;
    } else if (data && typeof data === 'object') {
      packages = data.data || [];
    }
    
    console.log('Tour packages API response:', { 
      isArray: Array.isArray(data), 
      hasDataKey: !!(data && data.data), 
      packagesCount: packages.length,
      dataKeys: data ? Object.keys(data) : [],
      rawData: data
    });
    
    if (packages && packages.length > 0) {
      // Sort to show featured first, then by display_order
      const sortedPackages = [...packages].sort((a: any, b: any) => {
        if (a.is_featured && !b.is_featured) return -1;
        if (!a.is_featured && b.is_featured) return 1;
        return (a.display_order || 0) - (b.display_order || 0);
      });
      
      tourPackages.value = sortedPackages.slice(0, 6).map((pkg: any) => ({
        id: pkg.id,
        slug: pkg.slug,
        title: pkg.title || '',
        short_description: pkg.short_description || null,
        price_from: pkg.price_from || null,
        duration_days: pkg.duration_days || null,
        max_participants: pkg.max_participants || null,
        is_featured: pkg.is_featured || false,
        hero_image: pkg.hero_image || null,
        gallery: pkg.gallery || [],
        images: pkg.images || [],
      }));
      
      console.log('Tour packages loaded successfully:', tourPackages.value.length, tourPackages.value);
    } else {
      console.warn('No tour packages found in API response. Packages array:', packages);
    }
  } catch (error) {
    console.error('Error fetching tour packages (network/timeout/error):', error);
    // Set empty array on error to show empty state
    tourPackages.value = [];
  } finally {
    isLoadingTourPackages.value = false;
  }

  // Fetch contact channels
  try {
    const response = await fetchWithTimeout('/api/contact-channels', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    const channels = Array.isArray(data) ? data : (data.data || []);
    
    if (channels.length > 0) {
      contactChannels.value = channels.map((channel: any) => ({
        label: channel.label || '',
        value: channel.value || '',
        detail: channel.detail || '',
      }));
    } else {
      // Fallback to defaults
      contactChannels.value = [
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
    }
  } catch (error) {
    console.error('Error fetching contact channels:', error);
    // Fallback to defaults
    contactChannels.value = [
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
  }

  // Fetch contact quick facts
  try {
    const response = await fetchWithTimeout('/api/contact-quick-facts', {}, 8000);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    const facts = Array.isArray(data) ? data : (data.data || []);
    
    if (facts.length > 0) {
      contactQuickFacts.value = facts.map((fact: any) => fact.fact || fact);
    } else {
      // Fallback to defaults
      contactQuickFacts.value = [
        'Dedicated concierge from pre-trip briefing to touchdown back home.',
        'Access to a private guest portal with live itinerary updates.',
        'Emergency response network spanning Tanzania and Zanzibar.',
      ];
    }
  } catch (error) {
    console.error('Error fetching contact quick facts:', error);
    // Fallback to defaults
    contactQuickFacts.value = [
      'Dedicated concierge from pre-trip briefing to touchdown back home.',
      'Access to a private guest portal with live itinerary updates.',
      'Emergency response network spanning Tanzania and Zanzibar.',
    ];
  }
});

const currentSlide = ref(0);
const activeSlide = computed<HeroSlide>(() => {
  if (heroSlides.value.length === 0) {
    return {
      image: '/images/safari/hero-1.jpg',
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
  
  // Show back-to-top button when scrolled down more than 300px
  showBackToTop.value = currentScrollY > 300;
  
  lastScrollY.value = currentScrollY;
};

// Scroll to top function
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

// Contact form submission handler
const handleContactSubmit = async () => {
  contactFormError.value = null;
  contactFormSuccess.value = false;
  isSubmittingContact.value = true;

  try {
    const response = await fetch('/api/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        name: contactForm.value.name,
        email: contactForm.value.email,
        phone: contactForm.value.phone || null,
        service_type_id: contactForm.value.service_type_id || null,
        message: contactForm.value.message,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to send message. Please try again.');
    }

    // Success
    contactFormSuccess.value = true;
    contactForm.value = {
      name: '',
      email: '',
      phone: '',
      travelers: '',
      service_type_id: '',
      message: '',
    };

    // Scroll to success message
    setTimeout(() => {
      const formElement = document.querySelector('#contact form');
      if (formElement) {
        formElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    }, 100);
  } catch (error: any) {
    console.error('Error submitting contact form:', error);
    contactFormError.value = error.message || 'Failed to send message. Please try again.';
  } finally {
    isSubmittingContact.value = false;
  }
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

// Removed SafariPackage type and safariPackages - using tourPackages instead

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

type TourPackage = {
  id: number;
  slug: string;
  title: string;
  short_description: string | null;
  price_from: number | string | null;
  duration_days: number | null;
  max_participants: number | null;
  is_featured: boolean;
  hero_image: { url: string; thumb: string; cover: string } | null;
  gallery: Array<{ id: number; url: string; thumb: string; cover: string }>;
  images: string[];
};

const tourPackages = ref<TourPackage[]>([]);
const isLoadingTourPackages = ref(true);

// Contact form state
const contactForm = ref({
  name: '',
  email: '',
  phone: '',
  travelers: '',
  service_type_id: '',
  message: '',
});

const isSubmittingContact = ref(false);
const contactFormError = ref<string | null>(null);
const contactFormSuccess = ref(false);

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

const contactChannels = ref<Array<{ label: string; value: string; detail: string }>>([]);

const contactQuickFacts = ref<string[]>([]);

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
  <div class="min-h-screen text-charcoal m-0 p-0">
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
              href="#destinations"
            >
              Destinations
            </a>
            <router-link 
              to="/tour-packages"
              class="rounded-full border border-safari-gold/40 bg-black/60 backdrop-blur-md px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.25em] text-white shadow-medium transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:border-safari-gold hover:shadow-glow-gold hover:scale-110" 
            >
              Safaris
            </router-link>
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

    <main class="pt-0 m-0">
      <section id="home" class="relative m-0">
        <div class="relative h-screen min-h-[600px] m-0">
          <div
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
              loading="eager"
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

          <div v-if="heroSlides.length > 0" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

          <div v-if="heroSlides.length > 0" class="absolute inset-x-0 bottom-10 z-20 flex items-center justify-between px-6">
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

      <!-- Smooth transition curve from hero -->
      <div class="relative -mt-1 h-24 overflow-hidden bg-gradient-to-b from-black/20 via-safari-sand/40 to-white">
        <svg class="absolute bottom-0 w-full" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
      </div>

      <section
        ref="featureSection"
        :class="[
          'relative overflow-hidden bg-gradient-to-b from-white via-safari-sand/5 to-white py-20 sm:py-28 transition-all duration-700 ease-out',
          featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10',
        ]"
      >
        <!-- Very faded background image -->
        <div class="absolute inset-0 opacity-[0.2] z-0">
          <img
            src="/images/safari/wildlife-herd.jpg"
            alt=""
            class="h-full w-full object-cover object-center"
            loading="lazy"
            @error="(e) => { (e.target as HTMLImageElement).src = '/images/safari/beach-1.jpg'; }"
          />
          <div class="absolute inset-0 bg-gradient-to-b from-white/25 via-white/10 to-white/25"></div>
        </div>
        
        <!-- Enhanced background patterns -->
        <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_top,rgba(217,154,56,0.06),transparent_60%)]"></div>
        <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(31,59,43,0.03),transparent_60%)]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-0 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/20 to-transparent"></div>
        
        <div class="relative z-10 mx-auto max-w-6xl px-6">
          <!-- Header with improved spacing and visual hierarchy -->
          <div class="text-center mb-16 sm:mb-20">
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
              <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                Our Promise
              </p>
              <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
            </div>
            <h2 class="mt-4 text-4xl font-heading font-bold text-charcoal sm:text-5xl lg:text-6xl text-balance leading-tight">
              {{ featureSectionTitle }}
            </h2>
            <p class="mt-6 text-lg sm:text-xl leading-relaxed text-charcoal/70 max-w-3xl mx-auto">
              {{ featureSectionSubtitle }}
            </p>
          </div>

          <!-- Redesigned feature cards with better integration -->
          <div class="relative mx-auto grid max-w-7xl gap-6 sm:gap-8 px-6 md:grid-cols-3">
            <article
              v-for="(feature, index) in featureCards"
              :key="feature.title"
              class="group relative rounded-2xl sm:rounded-3xl border border-safari-sand/30 bg-white/95 backdrop-blur-md p-8 sm:p-10 text-center transition-all duration-500 ease-out shadow-sm hover:-translate-y-3 hover:shadow-2xl hover:shadow-safari-gold/20 hover:border-safari-gold/50"
              :class="featureVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
              :style="{ transitionDelay: featureVisible ? `${index * 120}ms` : '0ms' }"
              @mouseenter="feature.icon === 'travellers' && boostTravellerCount()"
              @mouseleave="feature.icon === 'travellers' && settleTravellerCount()"
            >
              <!-- Enhanced gradient overlay -->
              <div class="absolute inset-0 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-safari-gold/8 via-transparent to-safari-green/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
              
              <!-- Icon container with improved design -->
              <div class="relative mx-auto flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-xl sm:rounded-2xl border-2 border-safari-gold/40 bg-gradient-to-br from-safari-gold/5 via-white to-safari-sand/10 text-safari-gold shadow-md transition-all duration-300 group-hover:scale-110 group-hover:border-safari-gold group-hover:shadow-lg group-hover:shadow-safari-gold/30 group-hover:bg-gradient-to-br group-hover:from-safari-gold/10 group-hover:via-white group-hover:to-safari-gold/5">
                <component :is="getFeatureIcon(feature.icon)" />
              </div>
              
              <!-- Content -->
              <template v-if="feature.icon === 'travellers'">
                <p class="mt-6 sm:mt-8 text-4xl sm:text-5xl font-heading font-bold text-safari-green transition-transform duration-300 group-hover:scale-105">
                  {{ travellerCount.toLocaleString() }}
                  <span class="text-2xl sm:text-3xl font-bold text-safari-gold">+</span>
                </p>
                <h3 class="mt-4 text-xl sm:text-2xl font-semibold tracking-tight text-charcoal">
                  {{ feature.title }}
                </h3>
              </template>
              <template v-else>
                <h3 class="mt-6 sm:mt-8 text-xl sm:text-2xl font-semibold tracking-tight text-charcoal">
                  {{ feature.title }}
                </h3>
              </template>
              
              <!-- Decorative divider -->
              <div class="mx-auto mt-5 sm:mt-6 h-0.5 w-20 bg-gradient-to-r from-transparent via-safari-gold/60 to-transparent transition-all duration-300 group-hover:w-24 group-hover:via-safari-gold"></div>
              
              <p class="mt-5 sm:mt-6 text-sm sm:text-base leading-relaxed text-charcoal/70">
                {{ feature.copy }}
              </p>
              
              <!-- Subtle bottom accent -->
              <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-0 bg-gradient-to-r from-safari-gold to-safari-green transition-all duration-500 group-hover:w-3/4 rounded-full"></div>
            </article>
          </div>
        </div>
      </section>

      <section id="about" class="relative overflow-hidden bg-gradient-to-b from-charcoal via-charcoal-dark to-charcoal text-white py-24 sm:py-32">
        <!-- Dark background with subtle image -->
        <div class="absolute inset-0 opacity-[0.12] z-0">
          <img
            src="/images/safari/wildlife-zebra.jpg"
            alt=""
            class="h-full w-full object-cover object-center"
            loading="lazy"
            @error="(e) => { (e.target as HTMLImageElement).src = '/images/safari/beach-1.jpg'; }"
          />
          <div class="absolute inset-0 bg-gradient-to-b from-charcoal/90 via-charcoal/85 to-charcoal/90"></div>
        </div>
        
        <!-- Enhanced background patterns -->
        <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_top_left,rgba(217,154,56,0.15),transparent_60%)]"></div>
        <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(31,59,43,0.12),transparent_60%)]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-0 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/40 to-transparent"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-6">
          <div class="grid gap-12 lg:gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div class="space-y-6">
              <div class="inline-flex items-center gap-3">
                <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
                <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                  Authentic Tanzanian Expertise
                </p>
                <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
              </div>
              <h2 class="text-4xl font-heading font-bold text-white leading-tight sm:text-5xl lg:text-6xl text-balance">
                Journeys designed by locals who live, breathe, and protect Tanzania
              </h2>
              <p class="text-lg sm:text-xl leading-relaxed text-white/85 max-w-2xl">
                We move beyond brochure itineraries. Our Dar es Salaam and Arusha teams collaborate daily
                with guides, lodge owners, and conservation partners to secure privileged access and real-time
                intelligence. The result: safaris that feel effortless, immersive, and entirely your own.
              </p>
              <div class="flex flex-wrap gap-4 pt-4">
                <a
                  href="#contact"
                  class="group relative overflow-hidden rounded-full bg-gradient-to-r from-safari-green to-safari-green/90 px-8 py-4 text-sm font-semibold text-white transition-all duration-300 hover:from-charcoal hover:to-charcoal/90 hover:shadow-xl hover:shadow-safari-green/30 hover:scale-105"
                >
                  <span class="relative z-10 flex items-center">
                    Speak to a Journey Architect
                    <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                  </span>
                </a>
                <a
                  href="#packages"
                  class="group rounded-full border-2 border-safari-gold bg-white/10 backdrop-blur-sm px-8 py-4 text-sm font-semibold text-safari-gold transition-all duration-300 hover:bg-safari-gold hover:text-charcoal hover:shadow-lg hover:shadow-safari-gold/30 hover:scale-105"
                >
                  View Safaris
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
              </div>
            </div>
            <div class="rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/10 via-white/8 to-white/5 backdrop-blur-md p-8 sm:p-10 shadow-xl transition-all duration-300 hover:border-safari-gold/50 hover:shadow-2xl hover:shadow-safari-gold/20">
              <ul class="grid gap-5 sm:gap-6 sm:grid-cols-3 lg:grid-cols-1">
                <li
                  v-for="stat in aboutStats"
                  :key="stat.label"
                  class="group relative rounded-xl sm:rounded-2xl bg-gradient-to-br from-white/15 via-white/10 to-white/5 border border-white/20 px-6 sm:px-8 py-5 sm:py-6 text-center transition-all duration-300 hover:shadow-lg hover:scale-105 hover:from-safari-gold/20 hover:via-white/15 hover:to-white/10 hover:border-safari-gold/40"
                >
                  <p class="text-3xl sm:text-4xl font-heading font-bold text-safari-gold transition-transform duration-300 group-hover:scale-110">{{ stat.value }}</p>
                  <p class="mt-3 text-xs font-semibold uppercase tracking-[0.25em] text-white/80" v-html="stat.label"></p>
                  <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-0.5 w-0 bg-gradient-to-r from-safari-gold to-transparent transition-all duration-300 group-hover:w-full rounded-full"></div>
                </li>
              </ul>
              <div class="mt-8 sm:mt-10 rounded-xl sm:rounded-2xl border border-safari-gold/40 bg-gradient-to-br from-safari-gold/15 via-safari-gold/10 to-transparent p-7 sm:p-8 transition-all duration-300 hover:border-safari-gold/60 hover:shadow-lg hover:shadow-safari-gold/20">
                <div class="flex items-center gap-3 mb-4">
                  <div class="h-px flex-1 bg-gradient-to-r from-safari-gold/80 to-transparent"></div>
                  <p class="text-sm font-bold uppercase tracking-[0.35em] text-safari-gold">Accreditations</p>
                  <div class="h-px flex-1 bg-gradient-to-l from-safari-gold/80 to-transparent"></div>
                </div>
                <p class="text-sm leading-relaxed text-white/90">
                  Proud members of the Tanzania Association of Tour Operators (TATO), ATTA, and Leave No Trace. We
                  hand-select suppliers championing eco-conscious luxury.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-16 sm:mt-20 grid gap-6 sm:gap-8 md:grid-cols-3">
            <article
              v-for="highlight in aboutHighlights"
              :key="highlight.title"
              class="group relative rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-md p-7 sm:p-8 transition-all duration-500 hover:-translate-y-2 hover:border-safari-gold/50 hover:shadow-2xl hover:shadow-safari-gold/20"
            >
              <div class="absolute inset-0 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-safari-gold/15 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
              <div class="relative inline-flex rounded-full bg-gradient-to-r from-safari-gold/25 to-safari-gold/15 border border-safari-gold/40 px-5 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white shadow-sm transition-all duration-300 group-hover:border-safari-gold/60 group-hover:shadow-md">
                Core Pillar
              </div>
              <h3 class="mt-6 text-xl sm:text-2xl font-heading font-bold text-white transition-transform duration-300 group-hover:scale-105">{{ highlight.title }}</h3>
              <p class="mt-4 text-base leading-relaxed text-white/80">
                {{ highlight.copy }}
              </p>
              <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-0 bg-gradient-to-r from-safari-gold to-safari-green transition-all duration-500 group-hover:w-3/4 rounded-full"></div>
            </article>
          </div>
        </div>
      </section>

      <section id="packages" class="relative overflow-hidden bg-gradient-to-b from-safari-green via-safari-green-dark to-safari-green py-32 text-white">
        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10">
          <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.2) 1px, transparent 0); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Subtle overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/40 via-transparent to-black/30"></div>
        
        <div class="relative mx-auto max-w-7xl px-6">
          <!-- Header Section -->
          <div class="mb-16 sm:mb-20 text-center">
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
              <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                Safaris and Itineraries
              </p>
              <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
            </div>
            <h2 class="text-4xl sm:text-5xl font-heading font-bold text-white leading-tight lg:text-6xl xl:text-7xl mb-6 sm:mb-8 text-balance">
              Preview a few of our most-requested<br />
              <span class="text-safari-gold">tailored itineraries</span>
            </h2>
            <p class="mx-auto max-w-3xl text-lg sm:text-xl leading-relaxed text-white/90">
              Every journey is individually reimagined around your pace, interests, and travel style. Use these curated
              blueprints as inspiration—our team refines them into a completely bespoke adventure.
            </p>
          </div>

          <!-- Tour Packages Grid -->
          <div v-if="tourPackages.length > 0" class="grid gap-10 md:grid-cols-2 lg:grid-cols-3 mb-20">
            <article
              v-for="(pkg, index) in tourPackages"
              :key="pkg.id || pkg.title || index"
              @click="() => pkg.slug && router.push(`/tour-packages/${pkg.slug}`)"
              class="group relative flex flex-col overflow-hidden rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/10 via-white/8 to-white/5 backdrop-blur-md shadow-xl transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.6)] hover:bg-gradient-to-br hover:from-white/15 hover:via-white/12 hover:to-white/8 hover:border-white/40 hover:border-safari-gold/30 cursor-pointer"
            >
              <!-- Image Container -->
              <div class="relative h-64 overflow-hidden">
                <img
                  :src="pkg.hero_image?.cover || pkg.hero_image?.url || pkg.images?.[0] || '/images/safari/beach-1.jpg'"
                  :alt="pkg.title"
                  class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                  loading="lazy"
                  @error="(e) => { (e.target as HTMLImageElement).src = '/images/safari/beach-1.jpg'; }"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                
                <!-- Badge -->
                <div class="absolute left-6 top-6 z-10">
                  <span class="inline-flex items-center rounded-full bg-safari-gold px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-charcoal shadow-glow-gold backdrop-blur-sm">
                    {{ pkg.is_featured ? 'Featured' : 'Package' }}
                  </span>
                </div>

                <!-- Meta Info Overlay -->
                <div class="absolute inset-x-6 bottom-6">
                  <div class="flex items-center gap-3 text-sm font-semibold text-white">
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
                      Max {{ pkg.max_participants }}
                    </span>
                  </div>
                  <p v-if="pkg.price_from" class="mt-2 text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold/90">
                    From ${{ Number(pkg.price_from).toLocaleString() }}
                  </p>
                </div>
              </div>

              <!-- Content -->
              <div class="flex flex-1 flex-col gap-5 p-8">
                <div>
                  <h3 class="text-2xl font-heading text-white mb-3 leading-tight">{{ pkg.title }}</h3>
                  <p v-if="pkg.short_description" class="text-sm leading-relaxed text-white/75 line-clamp-3">
                    {{ pkg.short_description }}
                  </p>
                </div>

                <!-- Highlights -->
                <div class="mt-2 space-y-3">
                  <ul class="space-y-2.5">
                    <li v-if="pkg.duration_days" class="flex items-start gap-3">
                      <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-safari-gold text-xs font-bold text-charcoal">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </span>
                      <p class="flex-1 text-xs leading-relaxed text-white/85">
                        {{ pkg.duration_days }} day{{ pkg.duration_days > 1 ? 's' : '' }} immersive experience
                      </p>
                    </li>
                    <li v-if="pkg.max_participants" class="flex items-start gap-3">
                      <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-safari-gold text-xs font-bold text-charcoal">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                      </span>
                      <p class="flex-1 text-xs leading-relaxed text-white/85">
                        Small group experience (max {{ pkg.max_participants }} participants)
                      </p>
                    </li>
                    <li v-if="pkg.is_featured" class="flex items-start gap-3">
                      <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-safari-gold text-xs font-bold text-charcoal">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                      </span>
                      <p class="flex-1 text-xs leading-relaxed text-white/85">
                        Featured package with premium inclusions
                      </p>
                    </li>
                    <li class="flex items-start gap-3">
                      <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-safari-gold text-xs font-bold text-charcoal">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </span>
                      <p class="flex-1 text-xs leading-relaxed text-white/85">
                        Fully customizable itinerary
                      </p>
                    </li>
                  </ul>
                </div>

                <!-- Price & Actions -->
                <div class="mt-auto pt-6 border-t border-white/10">
                  <div v-if="pkg.price_from" class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60 mb-1">Starting from</p>
                    <p class="text-2xl font-bold text-safari-gold">
                      ${{ Number(pkg.price_from).toLocaleString() }}
                    </p>
                  </div>
                  <div class="flex items-center justify-center" @click.stop>
                    <router-link
                      :to="`/tour-packages/${pkg.slug}`"
                      class="group flex items-center justify-center rounded-full border-2 border-white/40 bg-white/5 backdrop-blur-sm px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:border-white hover:bg-white/20 hover:scale-105"
                    >
                      View Details
                      <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                      </svg>
                    </router-link>
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
          <div v-else-if="isLoadingTourPackages" class="grid gap-10 md:grid-cols-2 lg:grid-cols-3 mb-20">
            <div v-for="i in 3" :key="i" class="h-[600px] rounded-[2rem] bg-white/5 animate-pulse"></div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-16">
            <p class="text-white/60">No packages available at the moment. Please check back later.</p>
          </div>

          <!-- CTA Section -->
          <div class="mt-16 sm:mt-20 text-center">
            <router-link
              to="/tour-packages"
              class="group relative overflow-hidden inline-flex items-center gap-3 rounded-full bg-gradient-to-r from-safari-gold via-safari-gold/95 to-orange-500 px-8 sm:px-10 py-4 sm:py-5 text-sm sm:text-base font-bold text-charcoal shadow-lg transition-all duration-300 hover:from-safari-gold-light hover:via-safari-gold hover:to-orange-400 hover:shadow-2xl hover:shadow-safari-gold/40 hover:scale-105"
            >
              <span class="relative z-10 flex items-center">
                View All Packages
                <svg class="ml-2 h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-white/20 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
            </router-link>
          </div>
        </div>
      </section>

      <section id="destinations" class="relative overflow-hidden bg-gradient-to-b from-white via-safari-sand/10 to-white py-24 sm:py-32">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_70%_30%,rgba(217,154,56,0.06),transparent_60%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_20%_80%,rgba(31,59,43,0.04),transparent_60%)]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/20 to-transparent"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-end lg:justify-between mb-12 sm:mb-16">
            <div class="space-y-4 sm:space-y-5">
              <div class="inline-flex items-center gap-3">
                <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
                <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                  Iconic Destinations
                </p>
                <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
              </div>
              <h2 class="text-4xl font-heading font-bold text-charcoal leading-tight sm:text-5xl lg:text-6xl text-balance">
                Choose the landscapes that speak to your sense of wonder
              </h2>
              <p class="max-w-2xl text-lg sm:text-xl leading-relaxed text-charcoal/75">
                We orchestrate seamless multi-region journeys that stitch together the north's wildlife circuits,
                the remote south, and island escapes. Each region unlocks new textures, cultures, and wildlife moments.
              </p>
            </div>
            <a
              href="#lodges"
              class="group inline-flex items-center gap-3 self-start rounded-full border-2 border-safari-green bg-white/80 backdrop-blur-sm px-7 sm:px-8 py-3.5 sm:py-4 text-sm font-semibold text-safari-green transition-all duration-300 hover:bg-safari-green hover:text-white hover:shadow-lg hover:shadow-safari-green/20 hover:scale-105"
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

      <section id="lodges" class="relative overflow-hidden bg-gradient-to-b from-safari-sand/20 via-white to-safari-sand/10 py-24 sm:py-32">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_20%_80%,rgba(31,59,43,0.05),transparent_60%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_80%_20%,rgba(217,154,56,0.04),transparent_60%)]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/20 to-transparent"></div>
        <div class="relative mx-auto max-w-7xl px-6">
          <div class="grid gap-12 sm:gap-16 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div class="space-y-6 sm:space-y-8">
              <div class="space-y-5 sm:space-y-6">
                <div class="inline-flex items-center gap-3">
                  <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
                  <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                    Lodges &amp; Camps
                  </p>
                  <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
                </div>
                <h2 class="text-4xl font-heading font-bold text-charcoal leading-tight sm:text-5xl lg:text-6xl text-balance">
                  Hand-selected stays that marry safari romance with elevated comfort
                </h2>
                <p class="text-lg sm:text-xl leading-relaxed text-charcoal/75">
                  From treehouse suites and star-bed sleepouts to oceanfront sanctuaries, we recommend properties that align
                  with your style—whether that is contemporary design, authentic bushcraft, or family-friendly amenities.
                </p>
              </div>
              <ul class="space-y-4 sm:space-y-5 text-base text-charcoal/80">
                <li class="flex gap-4 group/item">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold transition-all duration-300 group-hover/item:scale-125 group-hover/item:shadow-sm"></span>
                  <span>Exclusive-use villas, honeymoon retreats, and multi-generational residences.</span>
                </li>
                <li class="flex gap-4 group/item">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold transition-all duration-300 group-hover/item:scale-125 group-hover/item:shadow-sm"></span>
                  <span>Wellness experiences: bush spas, mindfulness decks, and forest bathing trails.</span>
                </li>
                <li class="flex gap-4 group/item">
                  <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold transition-all duration-300 group-hover/item:scale-125 group-hover/item:shadow-sm"></span>
                  <span>Conservation levies and community visits woven into every stay.</span>
                </li>
              </ul>
              <div class="flex flex-wrap gap-4 pt-4">
                <a
                  href="#contact"
                  class="group relative overflow-hidden rounded-full bg-gradient-to-r from-safari-green to-safari-green/90 px-7 sm:px-8 py-3.5 sm:py-4 text-sm font-semibold text-white transition-all duration-300 hover:from-charcoal hover:to-charcoal/90 hover:shadow-xl hover:shadow-safari-green/30 hover:scale-105"
                >
                  <span class="relative z-10 flex items-center">
                    Request Availability
                    <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                  </span>
                </a>
                <a
                  href="#contact"
                  class="group rounded-full border-2 border-safari-green bg-white/80 backdrop-blur-sm px-7 sm:px-8 py-3.5 sm:py-4 text-sm font-semibold text-safari-green transition-all duration-300 hover:bg-safari-green hover:text-white hover:shadow-lg hover:shadow-safari-green/20 hover:scale-105"
                >
                  Build a Stay List
                  <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                </a>
              </div>
            </div>
            <div class="grid gap-6 sm:gap-8 sm:grid-cols-2">
              <article
                v-for="lodge in signatureLodges"
                :key="lodge.name"
                class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-safari-sand/30 bg-gradient-to-br from-white/90 via-white/80 to-white/70 backdrop-blur-md shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-safari-gold/50"
              >
                <div class="absolute inset-0 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-safari-gold/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="relative h-56 sm:h-64 overflow-hidden">
                  <img
                    :src="lodge.image"
                    :alt="lodge.name"
                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                    @error="(e) => { e.target.src = '/images/safari/beach-1.jpg'; }"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                  <div class="absolute left-5 sm:left-6 top-5 sm:top-6 rounded-full bg-white/90 backdrop-blur-sm border border-white/30 px-4 sm:px-5 py-1.5 sm:py-2 text-xs font-bold uppercase tracking-[0.3em] text-charcoal shadow-md transition-all duration-300 group-hover:border-safari-gold/50 group-hover:shadow-lg">
                    {{ lodge.location }}
                  </div>
                </div>
                <div class="relative flex h-full flex-col gap-4 sm:gap-5 p-6 sm:p-8">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                      :class="lodge.type === 'lodge' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'">
                      {{ lodge.type }}
                    </span>
                    <h3 class="text-xl sm:text-2xl font-heading font-bold text-charcoal transition-transform duration-300 group-hover:scale-105">{{ lodge.name }}</h3>
                  </div>
                  <p v-if="lodge.short_description" class="text-sm sm:text-base leading-relaxed text-charcoal/75">{{ lodge.short_description }}</p>
                  <p v-else-if="lodge.mood" class="text-sm sm:text-base leading-relaxed text-charcoal/75" v-html="lodge.mood"></p>
                  <div v-if="lodge.amenities && lodge.amenities.length > 0" class="flex flex-wrap gap-2">
                    <span
                      v-for="(amenity, index) in lodge.amenities.slice(0, 3)"
                      :key="index"
                      class="inline-flex rounded-full bg-safari-green/10 px-2 py-1 text-xs text-safari-green"
                    >
                      {{ amenity }}
                    </span>
                  </div>
                  <div class="mt-auto flex items-center justify-between pt-5 sm:pt-6 border-t border-safari-sand/40 text-sm font-bold uppercase tracking-[0.3em] text-safari-gold transition-all duration-300 group-hover:gap-4">
                    <span>Inquire</span>
                    <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                  </div>
                </div>
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-0 bg-gradient-to-r from-safari-gold to-safari-green transition-all duration-500 group-hover:w-3/4 rounded-full"></div>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section id="contact" class="relative overflow-hidden bg-gradient-to-b from-charcoal via-charcoal-dark to-charcoal text-white py-24 sm:py-32">
        <!-- Enhanced Background Effects -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(217,154,56,0.12),transparent_60%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(31,59,43,0.08),transparent_60%)]"></div>
        <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-[url('/images/safari/kelvin-zyteng-gxS48JsmH_0-unsplash.jpg')] bg-cover bg-center opacity-[0.08] lg:block"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/30 to-transparent"></div>
        
        <div class="relative mx-auto max-w-7xl px-6">
          <!-- Modern Section Header -->
          <div class="mb-16 text-center lg:mb-24">
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-12 bg-gradient-to-r from-transparent to-safari-gold/60"></div>
              <p class="text-xs font-bold uppercase tracking-[0.5em] text-safari-gold">
                Start Planning
              </p>
              <div class="h-px w-12 bg-gradient-to-l from-transparent to-safari-gold/60"></div>
            </div>
            <h2 class="mx-auto max-w-4xl text-4xl font-heading font-bold text-white leading-tight sm:text-5xl lg:text-6xl text-balance">
              Share your dream safari—we'll craft a tailored itinerary within 24 hours
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg sm:text-xl leading-relaxed text-white/80">
              Tell us who you are travelling with, the wildlife you are eager to witness, and your ideal travel window.
              We will reply with curated route ideas, accommodations, and investment options.
            </p>
          </div>

          <!-- Main Content Grid -->
          <div class="grid gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-16">
            <!-- Left Column: Contact Info & Quick Facts -->
            <div class="flex flex-col space-y-6 sm:space-y-8">
              <!-- Modern Contact Channels -->
              <div class="grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-1">
                <div
                  v-for="channel in contactChannels"
                  :key="channel.label"
                  class="group relative flex h-full flex-col rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/8 via-white/5 to-white/3 backdrop-blur-md p-6 sm:p-7 transition-all duration-500 hover:border-safari-gold/50 hover:bg-gradient-to-br hover:from-white/12 hover:via-white/8 hover:to-white/5 hover:shadow-xl hover:shadow-safari-gold/20 hover:-translate-y-1"
                >
                  <div class="absolute inset-0 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-safari-gold/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                  <div class="relative">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.35em] text-safari-gold">{{ channel.label }}</p>
                    <p class="mb-auto text-xl sm:text-2xl font-heading font-bold leading-tight text-white transition-transform duration-300 group-hover:scale-105">{{ channel.value }}</p>
                    <p class="mt-3 text-sm uppercase tracking-[0.2em] leading-relaxed text-white/70" v-html="channel.detail"></p>
                  </div>
                  <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-0.5 w-0 bg-gradient-to-r from-safari-gold to-transparent transition-all duration-500 group-hover:w-full rounded-full"></div>
                </div>
              </div>

              <!-- Enhanced Quick Facts Card -->
              <div class="rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/8 via-white/5 to-white/3 backdrop-blur-md p-7 sm:p-8 shadow-lg transition-all duration-300 hover:border-safari-gold/40 hover:shadow-xl hover:shadow-safari-gold/10">
                <div class="flex items-center gap-3 mb-6">
                  <div class="h-px flex-1 bg-gradient-to-r from-safari-gold/60 to-transparent"></div>
                  <p class="text-xs font-bold uppercase tracking-[0.35em] text-safari-gold">
                    Why Choose Us
                  </p>
                  <div class="h-px flex-1 bg-gradient-to-l from-safari-gold/60 to-transparent"></div>
                </div>
                <ul class="space-y-4 text-sm sm:text-base text-white/80">
                  <li
                    v-for="fact in contactQuickFacts"
                    :key="fact"
                    class="flex items-start gap-4 group/item"
                  >
                    <span class="mt-1.5 inline-flex h-2 w-2 flex-none rounded-full bg-safari-gold shadow-sm transition-all duration-300 group-hover/item:scale-125 group-hover/item:shadow-glow-gold"></span>
                    <span class="leading-relaxed flex-1">{{ fact }}</span>
                  </li>
                </ul>
              </div>

              <!-- Enhanced Location Card -->
              <div class="rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/8 via-white/5 to-white/3 backdrop-blur-md p-7 sm:p-8 shadow-lg transition-all duration-300 hover:border-safari-gold/40 hover:shadow-xl hover:shadow-safari-gold/10">
                <div class="flex items-center gap-3 mb-4">
                  <div class="h-px flex-1 bg-gradient-to-r from-safari-gold/60 to-transparent"></div>
                  <p class="text-xs font-bold uppercase tracking-[0.35em] text-safari-gold">
                    Meet us in person
                  </p>
                  <div class="h-px flex-1 bg-gradient-to-l from-safari-gold/60 to-transparent"></div>
                </div>
                <p class="mb-6 text-base leading-relaxed text-white/90">
                  Go Tanzania Safari Studio · Sokoine Road, Arusha 23100 · Visits by appointment only
                </p>
                <div class="h-48 sm:h-56 overflow-hidden rounded-xl sm:rounded-2xl border border-white/20 shadow-md transition-all duration-300 hover:border-safari-gold/40 hover:shadow-lg group">
                  <iframe
                    title="Go Tanzania Safari Studio Map"
                    src="https://maps.google.com/maps?q=Arusha%2C%20Tanzania&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    class="h-full w-full opacity-90 grayscale transition-all duration-500 group-hover:opacity-100 group-hover:grayscale-0"
                    loading="lazy"
                  ></iframe>
                </div>
              </div>
            </div>

            <!-- Right Column: Modern Contact Form -->
            <div class="lg:sticky lg:top-8 lg:h-fit">
              <form 
                @submit.prevent="handleContactSubmit"
                class="space-y-5 sm:space-y-6 rounded-2xl sm:rounded-3xl border border-white/20 bg-gradient-to-br from-white/10 via-white/8 to-white/5 backdrop-blur-md p-7 sm:p-9 lg:p-10 shadow-2xl transition-all duration-300 hover:border-safari-gold/30 hover:shadow-safari-gold/10"
              >
                <div class="mb-6 pb-6 border-b border-white/10">
                  <h3 class="text-2xl sm:text-3xl font-heading font-bold text-white mb-2">Get in Touch</h3>
                  <p class="text-sm sm:text-base text-white/70">Fill out the form below and we'll get back to you within 24 hours.</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                  <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="fullname">
                      Full Name
                    </label>
                    <input
                      id="fullname"
                      v-model="contactForm.name"
                      type="text"
                      placeholder="Your full name"
                      required
                      class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40"
                    />
                  </div>
                  <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="email">
                      Email Address
                    </label>
                    <input
                      id="email"
                      v-model="contactForm.email"
                      type="email"
                      placeholder="you@example.com"
                      required
                      class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40"
                    />
                  </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                  <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="phone">
                      Phone / WhatsApp
                    </label>
                    <input
                      id="phone"
                      v-model="contactForm.phone"
                      type="tel"
                      placeholder="+255..."
                      class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40"
                    />
                  </div>
                  <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="travelers">
                      Travellers
                    </label>
                    <input
                      id="travelers"
                      type="number"
                      min="1"
                      placeholder="2 adults, 2 kids..."
                      class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40"
                    />
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="service">
                    Experience Type
                  </label>
                  <select
                    id="service"
                    class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40"
                  >
                    <option class="text-charcoal bg-white" value="">Select an experience</option>
                    <option class="text-charcoal bg-white" value="wildlife">Wildlife Safari</option>
                    <option class="text-charcoal bg-white" value="kili">Kilimanjaro Expedition</option>
                    <option class="text-charcoal bg-white" value="coast">Coastal Retreat</option>
                    <option class="text-charcoal bg-white" value="honeymoon">Honeymoon Celebration</option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="block text-xs font-semibold uppercase tracking-[0.3em] text-safari-gold" for="message">
                    Tell Us More
                  </label>
                  <textarea
                    id="message"
                    v-model="contactForm.message"
                    rows="5"
                    placeholder="Preferred travel dates, bucket-list sightings, special celebrations..."
                    required
                    class="w-full rounded-xl sm:rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm px-4 sm:px-5 py-3 sm:py-3.5 text-white placeholder:text-white/50 transition-all duration-300 focus:border-safari-gold focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-safari-gold/50 hover:border-white/40 resize-none"
                  ></textarea>
                </div>

                <div v-if="contactFormError" class="mt-4 rounded-xl bg-red-500/20 border border-red-500/50 px-4 py-3 text-sm text-red-200">
                  {{ contactFormError }}
                </div>
                <div v-if="contactFormSuccess" class="mt-4 rounded-xl bg-green-500/20 border border-green-500/50 px-4 py-3 text-sm text-green-200">
                  Thank you! Your message has been sent. We'll get back to you within 24 hours.
                </div>
                <button
                  type="submit"
                  :disabled="isSubmittingContact"
                  class="group relative mt-6 sm:mt-8 w-full overflow-hidden rounded-full bg-gradient-to-r from-safari-gold via-safari-gold/95 to-orange-500 px-8 py-4 text-sm font-bold text-charcoal shadow-lg transition-all duration-300 hover:from-safari-gold-light hover:via-safari-gold hover:to-orange-400 hover:shadow-2xl hover:shadow-safari-gold/40 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="relative z-10 flex items-center justify-center">
                    Submit Inquiry
                    <svg class="ml-2 h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-white/20 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="relative bg-gradient-to-b from-white via-safari-sand/20 to-white border-t border-safari-sand/30">
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(217,154,56,0.04),transparent_60%)]"></div>
      <div class="absolute top-0 left-1/2 -translate-x-1/2 h-px w-3/4 bg-gradient-to-r from-transparent via-safari-gold/20 to-transparent"></div>
      <div class="relative mx-auto max-w-7xl px-6 py-10 sm:py-12">
        <div class="flex flex-col gap-6 border-t border-safari-sand/40 pt-8 md:flex-row md:items-center md:justify-between">
          <p class="text-base text-charcoal/70">© {{ new Date().getFullYear() }} Go Tanzania Safari Ltd. All rights reserved.</p>
          <div class="flex gap-5 sm:gap-6">
            <a class="group relative text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="Instagram">
              <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a class="group relative text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="Facebook">
              <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a class="group relative text-charcoal/60 transition-all duration-300 hover:text-safari-gold hover:scale-110" href="#" aria-label="TripAdvisor">
              <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Back to Top Button -->
    <button
      v-if="showBackToTop"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-safari-gold via-safari-gold/90 to-safari-gold/80 shadow-glow-gold transition-all duration-300 hover:scale-110 hover:shadow-glow-gold hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-safari-gold focus:ring-offset-2 group"
      aria-label="Back to top"
    >
      <svg
        class="h-6 w-6 text-charcoal transition-transform duration-300 group-hover:-translate-y-1"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        viewBox="0 0 24 24"
      >
        <path d="M5 10l7-7m0 0l7 7m-7-7v18" />
      </svg>
    </button>
  </div>
</template>

