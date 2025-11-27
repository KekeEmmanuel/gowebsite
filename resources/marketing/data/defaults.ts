/**
 * Default fallback data for when API calls fail or return empty results
 * This ensures the website always displays content even during network issues
 */

export const DEFAULT_DESTINATIONS = [
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

export const DEFAULT_ITINERARIES = [
  {
    title: 'Great Migration Serengeti Safari',
    summary: 'Follow the famed wildebeest march with private 4x4 game drives, exclusive viewing decks, and optional sunrise balloon flights.',
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
    summary: 'Pole pole ascent that blends private acclimatisation camps, chef-prepared cuisine, and summit-day oxygen support.',
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
    summary: 'Taste Stone Town\'s spice heritage then drift between private villa resorts, dhow cruises, and reef snorkelling.',
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

export const DEFAULT_LODGES = [
  {
    name: 'Four Seasons Safari Lodge',
    location: 'Serengeti Plains',
    mood: 'Waterhole-facing infinity pools & spa sanctuaries in the savannah canopy.',
    image: '/images/safari/lodge-1.jpg',
  },
  {
    name: 'Gibb\'s Farm Manor House',
    location: 'Ngorongoro Highlands',
    mood: 'Artist cottages, organic farm-to-table dining, and valley views wrapped in coffee estates.',
    image: '/images/safari/lodge-2.jpg',
  },
  {
    name: 'The Residence Zanzibar',
    location: 'Kizimkazi Peninsula',
    mood: 'Private pool villas, butler-led service, and azure lagoons inspired by Swahili heritage.',
    image: '/images/safari/beach-3.jpg',
  },
  {
    name: 'Chem Chem Lodge',
    location: 'Lake Manyara Corridor',
    mood: 'Slow safari philosophy with sunrise yoga decks, Maasai-led walks, and flamingo-dusted vistas.',
    image: '/images/safari/alferio-njau-MESNFA-pINg-unsplash.jpg',
  },
];

export const DEFAULT_FEATURE_CARDS = [
  {
    icon: 'travellers',
    title: 'Happy Travellers Yearly',
    headline: '500+ Happy Travellers Yearly',
    copy: 'Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.',
    count_value: 500,
  },
  {
    icon: 'support',
    title: '24/7 On-Call Support',
    headline: '24/7 On-Call Support',
    copy: 'Round-the-clock assistance from our dedicated team, ensuring peace of mind throughout your journey.',
    count_value: null,
  },
  {
    icon: 'expertise',
    title: 'Authentic Tanzanian Expertise',
    headline: 'Authentic Tanzanian Expertise',
    copy: 'Local knowledge and deep connections across Tanzania\'s most spectacular destinations.',
    count_value: null,
  },
];

export const DEFAULT_HERO_SLIDES = [
  {
    image: '/images/safari/wildlife-savannah.jpg',
    label: 'Welcome to Tanzania',
    title: 'Discover the beauty of Tanzania',
    description: 'Experience unforgettable safaris and adventures.',
    ctaLabel: 'Explore',
    ctaHref: '#safaris',
  },
];

/**
 * Helper function to fetch with timeout
 */
export async function fetchWithTimeout(url: string, options: RequestInit = {}, timeout: number = 10000): Promise<Response> {
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);
  
  try {
    const response = await fetch(url, {
      ...options,
      signal: controller.signal,
    });
    clearTimeout(timeoutId);
    return response;
  } catch (error) {
    clearTimeout(timeoutId);
    if (error instanceof Error && error.name === 'AbortError') {
      throw new Error('Request timeout');
    }
    throw error;
  }
}

