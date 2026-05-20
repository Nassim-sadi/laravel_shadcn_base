import type { AiContentField, AiModuleKey } from '@/services/api/ai-content.api'

export interface LocalValidationResult {
  valid: boolean
  errors: Array<{ row: number, errors: Record<string, string[]> }>
}

type ModuleDefinition = {
  label: string
  generatorFields: Array<{ key: AiContentField, label: string }>
  translatedFields: string[]
  requiredLocaleFields: string[]
  buildSampleRecord: (activeLocale: string) => Record<string, any>
}

function localeSet(activeLocale: string): string[] {
  return Array.from(new Set(['fr', activeLocale]))
}

function translatedValue(activeLocale: string, value: string): Record<string, string> {
  return localeSet(activeLocale).reduce<Record<string, string>>((accumulator, locale) => {
    accumulator[locale] = locale === activeLocale ? value : ''
    return accumulator
  }, {})
}

export const aiModuleConfig: Record<AiModuleKey, ModuleDefinition> = {
  services: {
    label: 'Services',
    generatorFields: [
      { key: 'title', label: 'Title' },
      { key: 'description', label: 'Description' },
      { key: 'seo_title', label: 'SEO title' },
      { key: 'seo_description', label: 'SEO description' },
      { key: 'seo_keywords', label: 'SEO keywords' },
    ],
    translatedFields: ['title', 'description', 'seo_title', 'seo_description', 'seo_keywords'],
    requiredLocaleFields: ['title'],
    buildSampleRecord: activeLocale => ({
      title: translatedValue(activeLocale, 'Premium advisory service'),
      description: translatedValue(activeLocale, 'A concise service description.'),
      icon: 'briefcase',
      url: 'https://example.com/service',
      order: 1,
      is_active: true,
      seo_title: translatedValue(activeLocale, 'Premium advisory service'),
      seo_description: translatedValue(activeLocale, 'Short SEO description'),
      seo_keywords: translatedValue(activeLocale, 'advisory, consulting'),
    }),
  },
  projects: {
    label: 'Projects',
    generatorFields: [
      { key: 'title', label: 'Title' },
      { key: 'description', label: 'Description' },
      { key: 'client', label: 'Client' },
      { key: 'seo_title', label: 'SEO title' },
      { key: 'seo_description', label: 'SEO description' },
      { key: 'seo_keywords', label: 'SEO keywords' },
    ],
    translatedFields: ['title', 'description', 'client', 'seo_title', 'seo_description', 'seo_keywords'],
    requiredLocaleFields: ['title'],
    buildSampleRecord: activeLocale => ({
      title: translatedValue(activeLocale, 'Clinic management platform'),
      description: translatedValue(activeLocale, 'A project summary for admins.'),
      client: translatedValue(activeLocale, 'Acme Clinic'),
      technologies: ['Laravel', 'Vue'],
      url: 'https://example.com/project',
      order: 1,
      is_active: true,
      seo_title: translatedValue(activeLocale, 'Clinic management platform'),
      seo_description: translatedValue(activeLocale, 'SEO summary for the project'),
      seo_keywords: translatedValue(activeLocale, 'clinic, management'),
    }),
  },
  faqs: {
    label: 'FAQs',
    generatorFields: [
      { key: 'question', label: 'Question' },
      { key: 'answer', label: 'Answer' },
      { key: 'seo_title', label: 'SEO title' },
      { key: 'seo_description', label: 'SEO description' },
    ],
    translatedFields: ['question', 'answer', 'seo_title', 'seo_description'],
    requiredLocaleFields: ['question', 'answer'],
    buildSampleRecord: activeLocale => ({
      question: translatedValue(activeLocale, 'How long does setup take?'),
      answer: translatedValue(activeLocale, 'Setup usually takes 2 to 3 business days.'),
      category: 'General',
      order: 1,
      is_active: true,
      seo_title: translatedValue(activeLocale, 'Setup timeline'),
      seo_description: translatedValue(activeLocale, 'SEO summary for the FAQ'),
    }),
  },
  testimonials: {
    label: 'Testimonials',
    generatorFields: [
      { key: 'name', label: 'Name' },
      { key: 'position', label: 'Position' },
      { key: 'company', label: 'Company' },
      { key: 'content', label: 'Content' },
      { key: 'seo_title', label: 'SEO title' },
      { key: 'seo_description', label: 'SEO description' },
    ],
    translatedFields: ['name', 'position', 'company', 'content', 'seo_title', 'seo_description'],
    requiredLocaleFields: ['name', 'content'],
    buildSampleRecord: activeLocale => ({
      name: translatedValue(activeLocale, 'Sara Martin'),
      position: translatedValue(activeLocale, 'Operations manager'),
      company: translatedValue(activeLocale, 'Acme Studio'),
      content: translatedValue(activeLocale, 'Working with the team felt smooth and dependable.'),
      rating: 5,
      order: 1,
      is_active: true,
      seo_title: translatedValue(activeLocale, 'Client testimonial'),
      seo_description: translatedValue(activeLocale, 'SEO summary for the testimonial'),
    }),
  },
  blog_posts: {
    label: 'Blog posts',
    generatorFields: [
      { key: 'title', label: 'Title' },
      { key: 'excerpt', label: 'Excerpt' },
      { key: 'body', label: 'Body' },
    ],
    translatedFields: ['title', 'excerpt', 'body'],
    requiredLocaleFields: ['title'],
    buildSampleRecord: activeLocale => ({
      title: translatedValue(activeLocale, 'How to prepare your team for launch'),
      slug: 'prepare-your-team-for-launch',
      excerpt: translatedValue(activeLocale, 'A short blog summary.'),
      body: translatedValue(activeLocale, '<p>First paragraph of the article.</p>'),
      is_published: true,
      featured: false,
    }),
  },
}

export function buildImportPrompt(module: AiModuleKey, activeLocale: string): string {
  const definition = aiModuleConfig[module]
  const sample = JSON.stringify([definition.buildSampleRecord(activeLocale)], null, 2)

  return [
    `Generate valid JSON for the ${definition.label.toLowerCase()} module.`,
    `Write the actual content in locale "${activeLocale}".`,
    'Return only raw JSON with a top-level array.',
    'Every row must match this shape exactly, and translated fields must be objects keyed by locale code.',
    'Important: the current admin validation still requires the "fr" translation for the primary required translated fields, so include it even if your active locale is different.',
    'Do not add comments, markdown fences, or explanations.',
    '',
    sample,
  ].join('\n')
}

export function buildSampleJson(module: AiModuleKey, activeLocale: string): string {
  return JSON.stringify([aiModuleConfig[module].buildSampleRecord(activeLocale)], null, 2)
}

export function validateImportJson(module: AiModuleKey, payload: unknown): LocalValidationResult {
  if (!Array.isArray(payload)) {
    return {
      valid: false,
      errors: [{ row: 0, errors: { file: ['The JSON file must contain a top-level array.'] } }],
    }
  }

  const definition = aiModuleConfig[module]
  const errors: Array<{ row: number, errors: Record<string, string[]> }> = []

  payload.forEach((row, index) => {
    const rowErrors: Record<string, string[]> = {}

    if (!row || typeof row !== 'object' || Array.isArray(row)) {
      errors.push({ row: index + 1, errors: { row: ['Each row must be a JSON object.'] } })
      return
    }

    for (const field of definition.requiredLocaleFields) {
      const value = (row as any)[field]
      if (!value || typeof value !== 'object' || Array.isArray(value)) {
        rowErrors[field] = [`The ${field} field must be a translated object.`]
        continue
      }

      if (typeof value.fr !== 'string' || value.fr.trim() === '') {
        rowErrors[field] = [...(rowErrors[field] ?? []), `The ${field}.fr value is required.`]
      }
    }

    for (const field of definition.translatedFields) {
      const value = (row as any)[field]
      if (value === undefined || value === null)
        continue

      if (typeof value !== 'object' || Array.isArray(value)) {
        rowErrors[field] = [...(rowErrors[field] ?? []), `The ${field} field must be an object keyed by locale.`]
      }
    }

    if (errors.length === 0 && Object.keys(rowErrors).length > 0) {
      errors.push({ row: index + 1, errors: rowErrors })
    }
    else if (Object.keys(rowErrors).length > 0) {
      errors.push({ row: index + 1, errors: rowErrors })
    }
  })

  return {
    valid: errors.length === 0,
    errors,
  }
}
