<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhattji/Urmi/blob/main/licence.md
*/

include 'header.php'; ?>

<div class="wrap" style="max-width:1100px;margin:auto;text-align:center">

    <div class="cards">

        <!-- Shared -->
        <div class="card" style="text-align:center">

            <div style="margin-bottom:10px">
                <div class="muted">Shared Streaming</div>
                <div class="price">₹2,000 / month</div>
                <div style="margin-top:6px">
                    <span class="pill">Best for small producers</span>
                </div>
            </div>

            <table>
                <tr>
                    <th>Feature</th>
                    <th>Included</th>
                </tr>
                <tr>
                    <td>Delivery formats</td>
                    <td>HLS (m3u8), RTMP, SRT, DASH — unlimited data for links</td>
                </tr>
                <tr>
                    <td>Bandwidth</td>
                    <td>Shared pool — burst-capable (fair-usage policy)</td>
                </tr>
                <tr>
                    <td>Domain</td>
                    <td>Shared Domain ( isp.urmic.org )</td>
                </tr>
                <tr>
                    <td>SSL</td>
                    <td>Let's Encrypt (shared certificate)</td>
                </tr>
                <tr>
                    <td>Support</td>
                    <td>Email & chat (business hours)</td>
                </tr>
                <tr>
                    <td>Uptime SLA</td>
                    <td>99.5%</td>
                </tr>
            </table>

            <div style="margin-top:14px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap">
                <a class="cta cta-primary" href="contact_us.php">Contact Us</a>
                <a class="cta cta-ghost" href="https://urmic.org/trusted-partners/">Our ISP Partners</a>
            </div>

        </div>


        <!-- Dedicated -->
        <div class="card" style="text-align:center">

            <div style="margin-bottom:10px">
                <div class="muted">Dedicated Streaming</div>
                <div class="price">₹4,000 / month</div>
                <div style="margin-top:6px">
                    <span class="pill">Recommended for events & scale</span>
                </div>
            </div>

            <table>
                <tr>
                    <th>Feature</th>
                    <th>Included</th>
                </tr>
                <tr>
                    <td>Delivery formats</td>
                    <td>HLS (m3u8), RTMP, SRT, DASH — unlimited data for links</td>
                </tr>
                <tr>
                    <td>Bandwidth</td>
                    <td>Dedicated bandwidth allocation (higher sustained throughput up to 10gbe spike )</td>
                </tr>
                <tr>
                    <td>Domain</td>
                    <td>Dedicated domain included (example: yourbrand.live)</td>
                </tr>
                <tr>
                    <td>SSL</td>
                    <td>Free SSL certificate (Let's Encrypt) + automated renewals</td>
                </tr>
                <tr>
                    <td>DDoS / Attack protection</td>
                    <td>Network-level mitigation & WAF</td>
                </tr>
                <tr>
                    <td>Static IP</td>
                    <td>Dedicated ip ipv4 and ipv6 available (reserved IP) — useful for whitelisting</td>
                </tr>
                <tr>
                    <td>Uptime SLA</td>
                    <td>99.9% with priority support</td>
                </tr>
                <tr>
                    <td>Support</td>
                    <td>24/7 priority support & onboarding</td>
                </tr>
            </table>

            <div style="margin-top:14px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap">
                <a class="cta cta-primary" href="contact_us.php">Contact Us</a>
                <a class="cta cta-ghost" href="https://urmic.org/trusted-partners/">Our ISP Partners</a>
            </div>

        </div>

    </div>

    <div class="card" style="margin-top:26px">
        <section class="benefits">
            <h3>Why choose hosted streaming over just buying a static IP from your ISP ?</h3>

            <ul style="display:inline-block;text-align:left;margin-top:12px">
                <li><strong>DDoS & attack protection:</strong> Professional hosts run network-level DDoS mitigation and web application firewalls (WAF) that absorb and block large-scale attacks before they reach your origin server.</li>
                <li><strong>Scalable bandwidth & CDN:</strong> Hosting + CDN provides globally distributed edge points and the ability to scale to many thousands of viewers without saturating a single home/office link.</li>
                <li><strong>Higher availability & SLA:</strong> Providers operate redundant infrastructure and SLAs that keep streams online even when single links or hardware fail.</li>
                <li><strong>Managed SSL, domain & DNS:</strong> Automated SSL issuance/renewal (Let's Encrypt), DNS features and a dedicated domain remove operational friction compared to configuring services on a raw ISP IP.</li>
                <li><strong>Security isolation:</strong> Dedicated servers and hosting accounts isolate your traffic and services from other customers, reducing risks that come with shared consumer-grade network equipment.</li>
                <li><strong>Monitoring & support:</strong> 24/7 monitoring, alerting and expert support are part of hosting plans — ISPs rarely provide application-level stream support.</li>
                <li><strong>Optional reserved (static) IPs:</strong> If you still need a static IP for whitelisting, we can provision a reserved IP on a dedicated plan and keep it behind our mitigation/CDN layer.</li>
            </ul>

            <div class="note">
                <strong>Quick notes:</strong>
                "Unlimited data for links" refers to stream delivery (no per-GB charge on the plan level for the specified formats). Extremely large egress (multi-TB per month) or abusive usage may require a custom enterprise agreement. CDN bandwidth, archival storage and advanced security may be subject to fair-use or tiered pricing.
            </div>

            <div class="note">
                <strong>Hosting :</strong>
                All servers are hosted with our CDN ISP partners. This project aims to transform ISPs into data-center service providers through a hybrid partnership model. All billing is handled directly by the ISP. We found this is lowest letency and stable solutions for broadcastors . Price includes GST and 2 month will be free on yearly payment .
            </div>
        </section>
    </div>
</div>


<?php include 'footer.php'; ?>