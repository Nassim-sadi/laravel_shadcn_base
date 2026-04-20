import { z } from 'zod'

export const billingSchema = z.object({
  id: z.number(),
  date: z.string(),
  amount: z.number(),
  plan: z.enum(['Free', 'Small Business', 'Enterprise']),
  status: z.enum(['paid', 'unpaid', 'overdue', 'cancelled']),
  file: z.string().optional(),
  description: z.string().optional(),
  orderId: z.string().optional(),
})

export type Billing = z.infer<typeof billingSchema>
