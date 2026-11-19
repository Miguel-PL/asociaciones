@extends('layouts.site')

@section('title', $page->title.' · '.$site->name)
@section('meta_description', $page->meta_description ?? $site->description)

@php
    $isCaudeteAboutPage = $site->slug === 'caudete-se-mueve'
        && $page->slug === 'quienes-somos'
        && trim((string) $page->body) === "Somos una asociación vecinal abierta a todo el barrio.\n\nExistimos para conocer los problemas concretos del entorno y para abrir canales de participación real, no solo consultiva.";
@endphp

@section('content')
    @if ($isCaudeteAboutPage)
        <article class="about-page" aria-labelledby="about-title">
            <section class="about-hero" aria-labelledby="about-title">
                <div class="about-hero-copy">
                    <p class="eyebrow"><span></span> Caudete Se Mueve</p>
                    <h1 id="about-title">{{ $page->title }}</h1>

                    <p class="about-intro">Caudete Se Mueve somos un colectivo vecinal y una organización sin ánimo de lucro nacida de la inquietud y la solidaridad de los ciudadanos de Caudete (Albacete). Creemos firmemente que, ante las crisis humanitarias, la injusticia social y la vulnerabilidad, la ciudadanía no puede quedarse de brazos cruzados:</p>

                    <p class="about-manifesto">debemos <span>movernos.</span></p>

                    <p class="about-continuation">Trabajamos de forma independiente, asamblearia y transparente, tejiendo redes de apoyo mutuo que conectan la solidaridad de nuestro pueblo con realidades complejas, tanto a la vuelta de la esquina como más allá de nuestras fronteras.</p>
                </div>

                @if ($image = $site->asset('quienes-somos/caudete-grupo-voluntarios.webp'))
                    <figure class="about-hero-photo">
                        <img src="{{ $image }}" alt="Voluntarias y voluntarios de Caudete Se Mueve colaboran en la entrega de ayuda humanitaria" fetchpriority="high">
                    </figure>
                @endif
            </section>

            <section class="about-principles" aria-label="Nuestra misión y nuestra visión">
                <div class="about-principles-inner">
                    <div class="about-principles-grid">
                        <section class="about-principle about-principle-mission" aria-labelledby="about-mission-title">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.8 8.6c0 5.1-8.8 10.2-8.8 10.2S3.2 13.7 3.2 8.6A4.5 4.5 0 0 1 12 6.4a4.5 4.5 0 0 1 8.8 2.2Z" />
                                        <path d="M7.3 12h2.2l1.2-2.1 2.2 4 1.2-1.9h2.5" />
                                    </svg>
                                </span>
                                <h2 id="about-mission-title">Nuestra misión</h2>
                            </div>
                            <p>Transformar la indignación en acción y la empatía en ayuda material y transformadora. Nos organizamos para dar respuestas rápidas y eficaces a emergencias humanitarias, garantizar la dignidad de las personas en situaciones de vulnerabilidad y concienciar a la sociedad civil para construir un mundo más justo y en paz.</p>
                        </section>

                        <section class="about-principle about-principle-vision" aria-labelledby="about-vision-title">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="2.5" />
                                    </svg>
                                </span>
                                <h2 id="about-vision-title">Nuestra visión</h2>
                            </div>
                            <p>Queremos ser un motor de cambio e inclusión en la comarca, consolidando un modelo de voluntariado activo, sostenible y en red. Aspiramos a una sociedad global donde los Derechos Humanos se defiendan activamente en la calle y las fronteras dejen de ser un obstáculo para la dignidad humana.</p>
                        </section>
                    </div>
                </div>
            </section>

            <section class="about-values" aria-labelledby="about-values-title">
                <div class="about-values-inner">
                    <header class="about-values-heading">
                        <h2 id="about-values-title">Nuestros valores</h2>
                        <p>Nuestra forma de actuar se basa en principios que guían nuestro compromiso con las personas, la solidaridad y la transformación social.</p>
                    </header>

                    <ul class="about-values-grid">
                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                            <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.8 8.6c0 5.1-8.8 10.2-8.8 10.2S3.2 13.7 3.2 8.6A4.5 4.5 0 0 1 12 6.4a4.5 4.5 0 0 1 8.8 2.2Z" />
                                    <path d="M12 8.8v5.4m-2.7-2.7h5.4" />
                                </svg>
                            </span>
                                <h3>Defensa Activa de los Derechos Humanos</h3>
                            </div>
                            <p>Los Derechos Humanos no se debaten, se defienden. Ponemos nuestro esfuerzo, nuestra voz y nuestros recursos al servicio de la dignidad de todas las personas, sin importar su origen, estatus o procedencia, entendiendo que los derechos universales deben ser garantizados en cualquier rincón del mundo.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 10 13-5v14L3 14v-4Z" />
                                    <path d="M16 9c2 .6 3 1.6 3 3s-1 2.4-3 3m-9-.8 1.4 5.3h3L10 16" />
                                </svg>
                            </span>
                                <h3>Denuncia de las Vulneraciones</h3>
                            </div>
                            <p>No somos cómplices del silencio. Sumamos a la acción humanitaria la labor irrenunciable de denunciar públicamente las injusticias, los genocidios, el racismo, la violencia de género y las violaciones de derechos que sufren los colectivos vulnerables y las personas en tránsito en las fronteras y rutas migratorias.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v18m-7-14h14M5 7l-3 6h6L5 7Zm14 0-3 6h6l-3-6ZM8 21h8" />
                                    <path d="M2 13a3 3 0 0 0 6 0m8 0a3 3 0 0 0 6 0" />
                                </svg>
                            </span>
                                <h3>Compromiso con la Justicia Social</h3>
                            </div>
                            <p>Trabajamos por un modelo social justo, equitativo e integrador. Entendemos la solidaridad no como un acto de caridad vertical, sino como una herramienta horizontal de transformación que busca erradicar las desigualdades estructurales, tanto a nivel local en nuestro municipio como en el ámbito internacional.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20s-7.5-4.2-7.5-10A4.4 4.4 0 0 1 12 7.2 4.4 4.4 0 0 1 19.5 10c0 5.8-7.5 10-7.5 10Z" />
                                    <path d="M3 17h5l2-2 2 2h5l3-2" />
                                </svg>
                            </span>
                                <h3>Acción Directa y Solidaridad</h3>
                            </div>
                            <p>No somos un simple escaparate de buenas intenciones. Recogemos, clasificamos, reparamos, cargamos contenedores y transportamos la ayuda humanitaria allí donde se necesita, optimizando cada recurso al máximo.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="5" r="2.5" />
                                    <circle cx="5" cy="18" r="2.5" />
                                    <circle cx="19" cy="18" r="2.5" />
                                    <path d="m10.8 7.2-4.5 8.6m6.9-8.6 4.5 8.6M7.5 18h9" />
                                </svg>
                            </span>
                                <h3>Trabajo en Red y Alianzas</h3>
                            </div>
                            <p>Creemos firmemente en la cooperación. Coordinamos esfuerzos de forma diaria con el tejido asociativo de nuestro municipio, plataformas de acción social de la provincia y redes de apoyo internacional, sumando de forma abierta con cualquier entidad para multiplicar el impacto social.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 7H3l4-4m-4 4 4 4M3 7h7a4 4 0 0 1 4 4" />
                                    <path d="M17 17h4l-4 4m4-4-4-4m4 4h-7a4 4 0 0 1-4-4" />
                                    <path d="m9 16 3-8 3 8m-5-3h4" />
                                </svg>
                            </span>
                                <h3>Economía Circular y Sostenibilidad</h3>
                            </div>
                            <p>Proyectos como nuestro taller solidario de bicicletas demuestran que la reutilización y el reciclaje material pueden transformarse de manera directa en autonomía, transporte digno e inserción social.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 9 10-5 10 5-10 5L2 9Z" />
                                    <path d="M6 11v5c3.6 2.5 8.4 2.5 12 0v-5m4-2v6" />
                                </svg>
                            </span>
                                <h3>Educación en Valores</h3>
                            </div>
                            <p>Creemos que para cambiar el futuro hay que remover conciencias en el presente. Por ello, entramos en las escuelas, institutos y centros juveniles para educar a las nuevas generaciones en la empatía, el voluntariado y la cultura de la paz.</p>
                        </li>
                    </ul>
                </div>
            </section>
        </article>
    @else
    <article class="editorial-page">
        <header class="page-heading">
        <p class="eyebrow">Caudete Se Mueve</p><h1>
            {{ $page->title }}
        </h1>
        </header>

        <div class="prose-body mt-8 text-stone-700">
            @include('public.partials.rich-content', ['content' => $page->body])
        </div>
    </article>
    @endif
@endsection
