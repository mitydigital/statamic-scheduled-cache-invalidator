# Scheduled Cache Invalidator for Statamic

<!-- statamic:hide -->

![Statamic 4+](https://img.shields.io/badge/Statamic-4+-FF269E?style=for-the-badge&link=https://statamic.com)
[![Scheduled Cache Invalidator for Statamic on Packagist](https://img.shields.io/packagist/v/mitydigital/statamic-scheduled-cache-invalidator?style=for-the-badge)](https://packagist.org/packages/mitydigital/statamic-scheduled-cache-invalidator/stats)

---

<!-- /statamic:hide -->

> A command to help invalidate the static cache when scheduled entries are due to go live.

## What is it?

Let's say you have a Blog, and your Statamic site uses full (or half) Static Caching.

Now, imagine you have written a blog post that you want to go live at midday tomorrow.

What would you prefer to do:

- wait around until midday tomorrow to manually click publish at 12:00 on the dot, or
- publish now, and have this utility take care of flushing the cache for you?

This command is designed to be run every minute, and looks for Entries (in all of your **dated** Collections) that are
scheduled to be published at that minute.

## Documentation

See the [documentation](https://docs.mity.com.au/scheduled-cache-invalidator) for detailed installation, configuration
and usage instructions.

## Testing

```bash
composer test
```

## Security

Security related issues should be emailed to [dev@mity.com.au](mailto:dev@mity.com.au) instead of logging an issue.

## Support

We love to share work like this, and help the community. However it does take time, effort and work.

The best thing you can do is [log an issue](../../issues).

Please try to be detailed when logging an issue, including a clear description of the problem, steps to reproduce the
issue, and any steps you may have tried or taken to overcome the issue too. This is an awesome first step to helping us
help you. So be awesome - it'll feel fantastic.

