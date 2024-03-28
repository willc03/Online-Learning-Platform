<x-structure.wrapper title="About">
    <style>
        .requirements-container {
            display: flex;
            flex-wrap: wrap;
        }
        .requirements-container .requirements {
            width: calc(50% - 20px);
            padding: 10px;
        }
        ul {
            list-style: none;
        }

        li {
            margin-bottom: 10px;
        }

        @media (max-width: 640px) {
            .requirements-container {
                flex-direction: column;
            }
            .requirements-container .requirements {
                width: 100%;
            }
        }
    </style>

    {{-- Title --}}
    <h1>About</h1>
    {{-- Introduction --}}
    <div class="introduction">
        <h2 class="title">{{ env('APP_NAME') }}</h2>
        <p>An application intended to be easily usable by users of all ages, from primary school students to adult education, and aims to blend aspects of a Virtual Learning Environment (VLE) and online assessment.</p>
    </div>
    {{-- Features & Functionality --}}
    <div class="features">
        <h2 class="title">Features</h2>
        <div class="introduction">
            <p>As in line with Chapter Three of the Project Report, the following of the MoSCoW requirements have been fulfilled.
                <span class="italicise">There is a detailed expansion of the fulfilled requirements in the Project Report.</span>
            </p>
        </div>
        <div class="requirements-container">
            <div class="requirements" id="must-have">
                <h3>Must Have</h3>
                <ul>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Users must be able to create an account by signing up to the website.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Users are able to browse available courses.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Users are able to join courses via the browsing page.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Users are able to create their own course(s).</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners must be able to organise their course into sections.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners must be able to add basic components to courses such as text and images.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />The application should utilise sessions to maintain a user authentication state.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />The application must prevent SQL injection.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />The application must prevent cross-site scripting (XSS), also referred to as cross-site request forgery (CSRF).</li>
                </ul>
            </div>
            <div class="requirements" id="should-have">
                <h3>Should Have</h3>
                <ul>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners should be able to access a settings page in which they can:</li>
                    <ul>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Edit the core settings of the course, such as the title or description.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Upload and remove custom content such as files.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Create, read, update, and delete (CRUD) invitation links.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />View and remove users who have joined the course.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Delete their course(s).</li>
                    </ul>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners should be able to add lessons to their course.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Lessons are modifiable such that owners can implement their own questions of varying types.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Users can join courses using a private invitation link if the course is not intended to be public.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners should be able to remove sections, or components within a section, of the course.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners should be able to edit the details of sections and change the order in which they appear.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />An ‘administrative view’ should be implemented on the course page.</li>
                </ul>
            </div>

            <div class="requirements" id="could-have">
                <h3>Could Have</h3>
                <ul>
                    <li><input type="checkbox" disabled class="right-spacer" />Allow lessons to save progress (optionally) so users can come back later.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Lessons should also be able to contain static items that are not questions, such as text or images.</li>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Lessons could implement some form of gamification.</li>
                    <ul>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Experience points (XP) could be implemented throughout the lesson.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />‘Hot streaks’ of correct answers could be implemented.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />A progression bar could be implemented so the user sees a form of progress throughout the lesson.</li>
                        <li><input type="checkbox" checked disabled class="right-spacer" />Designs of lesson questions could be given a cartoon-like appearance to improve user interaction.</li>
                    </ul>
                    <li><input type="checkbox" checked disabled class="right-spacer" />Course owners could have the ability to block users from the course.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Course owners can change the visibility of specific course sections or components within a section of the course.</li>
                </ul>
            </div>

            <div class="requirements" id="Would-have">
                <h3>Would Have</h3>
                <ul>
                    <li><input type="checkbox" disabled class="right-spacer" />Implement an AI learning algorithm to ensure users can practice weak points throughout their learning.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Implement an algorithm to suggest the user takes specific lessons based on percentage of interaction with sections of a course.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Implement functionality for a course owner to appoint course admins who can also access administrative pages.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Course owners can edit components in addition to adding and removing them.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Allow lessons to implement hints on certain questions at the designer’s discretion.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Users can implement a payment feature, referred to as a ‘paywall’, for either the entire course or specific sections.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Implement a course forum feature where users who take the same course can converse.</li>
                    <li><input type="checkbox" disabled class="right-spacer" />Implement a form of report button for users to flag inappropriate content.</li>
                </ul>
            </div>
        </div>
    </div>
    {{-- Target Audience --}}
    <div class="target-audience">
        <h2 class="title">Target Audience</h2>
        <p>
            The target audience for this application has been made very wide. The ability for users to create their own courses
            allows the application to cater to nearly all ages.
        </p>
        <p>
            One caveat may be that very young users, such as those under the age of eight, may struggle to understand the mechanics
            of the application. It would be the decision of teachers and/or parents if these users may attempt to use the application.
        </p>
        <h3>Privacy with varying audiences</h3>
        <p>
            In line with requirements both legally and ethically, the data of all users is protected. In the database, sensitive information
            such as the names and emails of users is encrypted. The Laravel framework intelligently and automatically decrypts these pieces
            of information when called upon for viewing.
        </p>
        <p>
            In this application's case, only the user's names will be presented in specific views. Users may see their own names as part of
            greeting messages, and course owners can view the names of users who take their course in the settings view in addition to within
            the lesson attempts page.
        </p>
    </div>
    {{-- Technologies --}}
    <div class="technologies">
        <h2 class="title">Technologies Used</h2>
        <h3>Laravel</h3>
        <p>
            First and foremost, this application is built on the Laravel framework. As explained in the <span class="italicise">Project Report</span>,
            this decision was taken as the industry norm is to use frameworks due to their extensive testing and utilities that provide aspects such as
            security and routing.
        </p>
        <p><span class="italicise">The use of Laravel is explained and demonstrated in-depth in the project report.</span></p>
        <h3>SCSS</h3>
        <p>
            Due to the large nature of the application, SCSS has been utilised to reduce the cumbersome nature of standard CSS. These SCSS files have
            automatically been compiled into one 'style.css' file by PhpStorm, the application used to facilitate the development of the application.
        </p>
        <h3>jQuery</h3>
        <p>
            jQuery has been utilised throughout the application to provide consistent appearances such as the floating course icons, or to provide
            animations such as when course elements are removed. One of the primary uses of jQuery has also been in providing the framework for
            elements such as 'sortables', which have been used to allow course owners to change the order of their lessons and courses, or date selectors,
            which allow the selection of a date and time in instances such as invite creation or edits.
        </p>
        <h3>NGINX Web Server</h3>
        <p>
            The NGINX web server is compatible with Laravel and provides a local development testing environment. Using this, always-active local server
            allows easy access using a local url, that is in this case <span class="italicise">https://learn.test/</span>, named after the directory the
            Laravel project is stored in.
        </p>
        <p>Laravel Valet, a version of Laravel specifically for macOS development, automatically set up the NGINX web server environment.</p>
    </div>
    {{-- Future Plans --}}
    <div class="future-development">
        <h2 class="title">Future Plans</h2>
        <p>
            Future development would primarily include the completion of the remaining could have requirements and all the would have requirements.
            Additionally, more detail would be paid to the styling, as more complex and intuitive client-side styling and design could be implemented,
            for example, the inclusion of more images throughout the site.
            <span class="italicise">Future plans are discussed in-depth in the Project Report.</span>
        </p>
    </div>
    {{-- Conclusion --}}
    <div class="conclusion">
        <h2>Further Information</h2>
        <p>A full, in-depth discussion of all aspects of the application and the development process can be found in the accompanying Project Report document.</p>
        <p>Version control has been utilised in this application. A <a href="https://github.com/willc03/Online-Learning-Platform">GitHub repository</a>
            is available and provides insight into the development process.
            <span class="italicise">This repository will be made public after the official deadline which, at the time of writing, is Tuesday 23rd April 2024.</span>
        </p>
        <h3>Contact</h3>
        <p>I can be contacted at <a href="mailto:40019692@student.furness.ac.uk">40019692@student.furness.ac.uk</a> or <a href="mailto:WCorkill@uclan.ac.uk">WCorkill@uclan.ac.uk</a>.</p>
    </div>
</x-structure.wrapper>
