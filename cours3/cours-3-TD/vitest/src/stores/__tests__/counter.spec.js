import { describe, it, expect, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useCounterStore } from '../counter'

describe('counter store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('starts at zero and increments', () => {
    const store = useCounterStore()

    expect(store.count).toBe(0)

    store.increment()

    expect(store.count).toBe(1)
  })

  it('decrements the count', () => {
    const store = useCounterStore()

    store.increment()
    store.decrement()

    expect(store.count).toBe(0)
  })
})
