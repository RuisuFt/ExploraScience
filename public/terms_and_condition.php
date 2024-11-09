<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login Page</title>
    
    <style>
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .toggle-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-size: 18px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .toggle-button:hover {
            background-color: #45a049;
        }

        .content {
            display: none;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-top: 10px;
        }

        .content p {
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="top-bar">
    <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
    <div class="title-box">
            <h2>Login Account</h2>
        </div>
        <div class="spacer"></div>
    </div>
    <div class="container">
        <button class="toggle-button" onclick="toggleContent('privacy')">Privacy Policy</button>
        <div id="privacy" class="content">
        <div class="terms-conditions-container">
    <h1 class="terms-title">Privacy Policy</h1>
    <p class="last-updated">Last updated: November 09, 2024</p>
    <p class="intro-text">This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>
    <p class="intro-text">We use Your Personal data to provide and improve the Service. By using the Service, You agree to the collection and use of information in accordance with this Privacy Policy. This Privacy Policy has been created with the help of the <a href="https://www.termsfeed.com/privacy-policy-generator/" target="_blank">Privacy Policy Generator</a>.</p>
    
    <h2 class="section-heading">Interpretation and Definitions</h2>
    
    <h3 class="subsection-heading">Interpretation</h3>
    <p class="definition-text">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>

    <h3 class="subsection-heading">Definitions</h3>
    <p class="definition-text">For the purposes of this Privacy Policy:</p>
    <ul>
        <li><p style="color: black;"><strong>Account</strong> means a unique account created for You to access our Service or parts of our Service.</p></li>
        <li><p style="color: black;"><strong>Affiliate</strong> means an entity that controls, is controlled by or is under common control with a party, where "control" means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p></li>
        <li><p style="color: black;"><strong>Company</strong> (referred to as either "the Company", "We", "Us" or "Our" in this Agreement) refers to Explorascience.</p></li>
        <li><p style="color: black;"><strong>Cookies</strong> are small files that are placed on Your computer, mobile device or any other device by a website, containing the details of Your browsing history on that website among its many uses.</p></li>
        <li><p style="color: black;"><strong>Country</strong> refers to: Philippines</p></li>
        <li><p style="color: black;"><strong>Device</strong> means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p></li>
        <li><p style="color: black;"><strong>Personal Data</strong> is any information that relates to an identified or identifiable individual.</p></li>
        <li><p style="color: black;"><strong>Service</strong> refers to the Website.</p></li>
        <li><p style="color: black;"><strong>Service Provider</strong> means any natural or legal person who processes the data on behalf of the Company. It refers to third-party companies or individuals employed by the Company to facilitate the Service, to provide the Service on behalf of the Company, to perform services related to the Service or to assist the Company in analyzing how the Service is used.</p></li>
        <li><p style="color: black;"><strong>Usage Data</strong> refers to data collected automatically, either generated by the use of the Service or from the Service infrastructure itself (for example, the duration of a page visit).</p></li>
        <li><p style="color: black;"><strong>Website</strong> refers to Explorascience, accessible from <a href="Explorascience.fun" target="_blank">Explorascience.fun</a></p></li>
        <li><p style="color: black;"><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p></li>
    </ul>

    <h2 class="section-heading">Collecting and Using Your Personal Data</h2>

    <h3 class="subsection-heading">Types of Data Collected</h3>
    <h4 class="subsection-heading">Personal Data</h4>
    <p class="definition-text">While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:</p>
    <ul>
        <li><p style="color: black;">Email address</p></li>
        <li><p style="color: black;">First name and last name</p></li>
        <li><p style="color: black;">Usage Data</p></li>
    </ul>

    <h4 class="subsection-heading">Usage Data</h4>
    <p class="definition-text">Usage Data is collected automatically when using the Service. Usage Data may include information such as Your Device's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that You visit, the time and date of Your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>

    <h4 class="subsection-heading">Tracking Technologies and Cookies</h4>
    <p class="definition-text">We use Cookies and similar tracking technologies to track the activity on Our Service and store certain information. Tracking technologies used are beacons, tags, and scripts to collect and track information and to improve and analyze Our Service. The technologies We use may include:</p>
    <ul>
        <li><strong>Cookies or Browser Cookies:</strong> A cookie is a small file placed on Your Device. You can instruct Your browser to refuse all Cookies or to indicate when a Cookie is being sent. However, if You do not accept Cookies, You may not be able to use some parts of our Service.</li>
        <li><strong>Web Beacons:</strong> Certain sections of our Service and our emails may contain small electronic files known as web beacons (also referred to as clear gifs, pixel tags, and single-pixel gifs) that permit the Company, for example, to count users who have visited those pages or opened an email and for other related website statistics.</li>
    </ul>

    <h3 class="subsection-heading">Use of Your Personal Data</h3>
    <p class="definition-text">The Company may use Personal Data for the following purposes:</p>
    <ul>
        <li><p style="color: black;"><strong>To provide and maintain our Service</strong>, including to monitor the usage of our Service.</p></li>
        <li><p style="color: black;"><strong>To manage Your Account:</strong> to manage Your registration as a user of the Service.</p></li>
        <li><p style="color: black;"><strong>For the performance of a contract:</strong> the development, compliance, and undertaking of the purchase contract for the products, items or services You have purchased or of any other contract with Us through the Service.</p></li>
        <li><p style="color: black;"><strong>To contact You:</strong> To contact You by email, telephone calls, SMS, or other equivalent forms of electronic communication.</p></li>
        <li><p style="color: black;"><strong>To provide You</strong> with news, special offers and general information about other goods, services and events.</p></li>
        <li><p style="color: black;"><strong>For business transfers:</strong> We may use Your information to evaluate or conduct a merger, divestiture, restructuring, reorganization, dissolution, or other sale or transfer of some or all of Our assets.</p></li>
        <li><p style="color: black;"><strong>For other purposes:</strong> We may use Your information for other purposes, such as data analysis, identifying usage trends, determining the effectiveness of our promotional campaigns and to evaluate and improve our Service, products, services, marketing, and your experience.</p></li>
    </ul>

    <h3 class="subsection-heading">Retention of Your Personal Data</h3>
    <p class="definition-text">The Company will retain Your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy.</p>

    <h3 class="subsection-heading">Transfer of Your Personal Data</h3>
    <p class="definition-text">Your information, including Personal Data, is processed at the Company's operating offices and in any other places where the parties involved in the processing are located.</p>

    <h3 class="subsection-heading">Delete Your Personal Data</h3>
    <p class="definition-text">You have the right to delete or request that We assist in deleting the Personal Data that We have collected about You.</p>

    <h3 class="subsection-heading">Disclosure of Your Personal Data</h3>
    <h4 class="subsection-heading">Business Transactions</h4>
    <p class="definition-text">If the Company is involved in a merger, acquisition or asset sale, Your Personal Data may be transferred. We will provide notice before Your Personal Data is transferred and becomes subject to a different Privacy Policy.</p>

    <h4 class="subsection-heading">Law enforcement</h4>
    <p class="definition-text">Under certain circumstances, the Company may be required to disclose Your Personal Data if required to do so by law or in response to valid requests by public authorities.</p>

    <h3 class="subsection-heading">Security of Your Personal Data</h3>
    <p class="definition-text">The security of Your Personal Data is important to Us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure.</p>

    <h2 class="section-heading">Children's Privacy</h2>
    <p class="definition-text">Our Service does not address anyone under the age of 13. We do not knowingly collect personally identifiable information from anyone under the age of 13.</p>

    <h2 class="section-heading">Links to Other Websites</h2>
    <p class="definition-text">Our Service may contain links to other websites that are not operated by Us. If You click on a third party link, You will be directed to that third party's site.</p>

    <h2 class="section-heading">Changes to this Privacy Policy</h2>
    <p class="definition-text">We may update Our Privacy Policy from time to time. We will notify You of any changes by posting the new Privacy Policy on this page.</p>

    <h2 class="section-heading">Contact Us</h2>
    <p class="definition-text">If you have any questions about this Privacy Policy, You can contact us:</p>
    <ul>
    <li><p style="color: black;">By email: bismontelm@gmail.com</p></li>
    </ul>
</div>
</div>

        <button class="toggle-button" onclick="toggleContent('terms')">Terms & Conditions</button>
        <div id="terms" class="content">
        <div class="terms-conditions-container">
    <h1 class="terms-title">Terms and Conditions</h1>
    <p class="last-updated">Last updated: November 09, 2024</p>
    <p class="intro-text">Please read these terms and conditions carefully before using Our Service.</p>

    <h2 class="section-heading">Interpretation and Definitions</h2>

    <h3 class="subsection-heading">Interpretation</h3>
    <p class="definition-text">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>

    <h3 class="subsection-heading">Definitions</h3>
    <p class="definition-text">For the purposes of these Terms and Conditions:</p>
    <ul>
        <li><p style="color: black;"><strong>Affiliate</strong> means an entity that controls, is controlled by or is under common control with a party, where "control" means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p></li>
        <li><p style="color: black;"><strong>Country</strong> refers to: Philippines</p></li>
        <li><p style="color: black;"><strong>Company</strong> (referred to as either "the Company", "We", "Us" or "Our" in this Agreement) refers to Explorascience.</p></li>
        <li><p style="color: black;"><strong>Device</strong> means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p></li>
        <li><p style="color: black;"><strong>Service</strong> refers to the Website.</p></li>
        <li><p style="color: black;"><strong>Terms and Conditions</strong> (also referred as "Terms") mean these Terms and Conditions that form the entire agreement between You and the Company regarding the use of the Service. This Terms and Conditions agreement has been created with the help of the <a href="https://www.termsfeed.com/terms-conditions-generator/" target="_blank">Terms and Conditions Generator</a>.</p></li>
        <li><p style="color: black;"><strong>Third-party Social Media Service</strong> means any services or content (including data, information, products or services) provided by a third-party that may be displayed, included or made available by the Service.</p></li>
        <li><p style="color: black;"><strong>Website</strong> refers to Explorascience, accessible from <a href="https://Explorascience.fun" target="_blank">Explorascience.fun</a></p></li>
        <li><p style="color: black;"><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p></li>
    </ul>

    <h2 class="section-heading">Acknowledgment</h2>
    <p class="definition-text">These are the Terms and Conditions governing the use of this Service and the agreement that operates between You and the Company. These Terms and Conditions set out the rights and obligations of all users regarding the use of the Service.</p>
    <p class="definition-text">Your access to and use of the Service is conditioned on Your acceptance of and compliance with these Terms and Conditions. These Terms and Conditions apply to all visitors, users, and others who access or use the Service.</p>
    <p class="definition-text">By accessing or using the Service You agree to be bound by these Terms and Conditions. If You disagree with any part of these Terms and Conditions then You may not access the Service.</p>
    <p class="definition-text">You represent that you are over the age of 18. The Company does not permit those under 18 to use the Service.</p>
    <p class="definition-text">Your access to and use of the Service is also conditioned on Your acceptance of and compliance with the Privacy Policy of the Company. Our Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your personal information when You use the Application or the Website and tells You about Your privacy rights and how the law protects You. Please read Our Privacy Policy carefully before using Our Service.</p>

    <h2 class="section-heading">Links to Other Websites</h2>
    <p class="definition-text">Our Service may contain links to third-party web sites or services that are not owned or controlled by the Company.</p>
    <p class="definition-text">The Company has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third-party web sites or services. You further acknowledge and agree that the Company shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with the use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>
    <p class="definition-text">We strongly advise You to read the terms and conditions and privacy policies of any third-party web sites or services that You visit.</p>

    <h2 class="section-heading">Termination</h2>
    <p class="definition-text">We may terminate or suspend Your access immediately, without prior notice or liability, for any reason whatsoever, including without limitation if You breach these Terms and Conditions.</p>
    <p class="definition-text">Upon termination, Your right to use the Service will cease immediately.</p>

    <h2 class="section-heading">Limitation of Liability</h2>
    <p class="definition-text">Notwithstanding any damages that You might incur, the entire liability of the Company and any of its suppliers under any provision of this Terms and Your exclusive remedy for all of the foregoing shall be limited to the amount actually paid by You through the Service or 100 USD if You haven't purchased anything through the Service.</p>
    <p class="definition-text">To the maximum extent permitted by applicable law, in no event shall the Company or its suppliers be liable for any special, incidental, indirect, or consequential damages whatsoever (including, but not limited to, damages for loss of profits, loss of data or other information, for business interruption, for personal injury, loss of privacy arising out of or in any way related to the use of or inability to use the Service, third-party software and/or third-party hardware used with the Service, or otherwise in connection with any provision of this Terms), even if the Company or any supplier has been advised of the possibility of such damages and even if the remedy fails of its essential purpose.</p>
    <p class="definition-text">Some states do not allow the exclusion of implied warranties or limitation of liability for incidental or consequential damages, which means that some of the above limitations may not apply. In these states, each party's liability will be limited to the greatest extent permitted by law.</p>

    <h2 class="section-heading">"AS IS" and "AS AVAILABLE" Disclaimer</h2>
    <p class="definition-text">The Service is provided to You "AS IS" and "AS AVAILABLE" and with all faults and defects without warranty of any kind. To the maximum extent permitted under applicable law, the Company, on its own behalf and on behalf of its Affiliates and its and their respective licensors and service providers, expressly disclaims all warranties, whether express, implied, statutory or otherwise, with respect to the Service, including all implied warranties of merchantability, fitness for a particular purpose, title and non-infringement, and warranties that may arise out of course of dealing, course of performance, usage or trade practice.</p>
    <p class="definition-text">Without limitation to the foregoing, the Company provides no warranty or undertaking, and makes no representation of any kind that the Service will meet Your requirements, achieve any intended results, be compatible or work with any other software, applications, systems or services, operate without interruption, meet any performance or reliability standards or be error free or that any errors or defects can or will be corrected.</p>
    <p class="definition-text">Without limiting the foregoing, neither the Company nor any of the company's provider makes any representation or warranty of any kind, express or implied: (i) as to the operation or availability of the Service, or the information, content, and materials or products included thereon; (ii) that the Service will be uninterrupted or error-free; (iii) as to the accuracy, reliability, or currency of any information or content provided through the Service; or (iv) that the Service, its servers, the content, or e-mails sent from or on behalf of the Company are free of viruses, scripts, trojan horses, worms, malware, timebombs or other harmful components.</p>

    <h2 class="section-heading">Governing Law</h2>
    <p class="definition-text">The laws of the Country, excluding its conflicts of law rules, shall govern this Terms and Your use of the Service. Your use of the Application may also be subject to other local, state, national, or international laws.</p>

    <h2 class="section-heading">Disputes Resolution</h2>
    <p class="definition-text">If You have any concern or dispute about the Service, You agree to first try to resolve the dispute informally by contacting the Company.</p>

    <h2 class="section-heading">Changes to These Terms and Conditions</h2>
    <p class="definition-text">We may update our Terms and Conditions from time to time. We will notify You of any changes by posting the new Terms and Conditions on this page.</p>
    <p class="definition-text">You are advised to review this Terms and Conditions periodically for any changes. Changes to these Terms and Conditions are effective when they are posted on this page.</p>

    <h2 class="section-heading">Contact Us</h2>
    <p class="definition-text">If you have any questions about these Terms and Conditions, please contact us:</p>
    <ul>
        <li>Email: <a href="mailto:support@explorascience.fun">support@explorascience.fun</a></li>
        <li>Website: <a href="https://Explorascience.fun">https://Explorascience.fun</a></li>
    </ul>
</div>

        </div>
        <form id="agreementForm" action="create_account.php" method="POST" onsubmit="return checkAgreement()">
    <label>
        <input type="checkbox" id="agreeCheckbox" onchange="toggleSubmitButton()" /> I agree to the 
        <a href="javascript:void(0);" onclick="toggleContent('privacy');">Privacy Policy</a> and 
        <a href="javascript:void(0);" onclick="toggleContent('terms');">Terms & Conditions</a>.
    </label>
    <br>
    <br>
    <button class="btn" type="submit" id="submitButton">Agree and Continue</button>
</form>
</div>

<script>
    // This function enables the submit button if the checkbox is checked
    function toggleSubmitButton() {
        var checkbox = document.getElementById('agreeCheckbox');
        var submitButton = document.getElementById('submitButton');
        submitButton.disabled = !checkbox.checked; // Disable button if checkbox is not checked
    }

    // This function checks if the checkbox is checked before allowing form submission
    function checkAgreement() {
        var checkbox = document.getElementById('agreeCheckbox');
        if (!checkbox.checked) {
            alert("You must agree to the Privacy Policy and Terms & Conditions before proceeding.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }

    // Optional: Show content (e.g., Privacy Policy or Terms) when the links are clicked
    function toggleContent(id) {
        var content = document.getElementById(id);
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block"; // Show the content
        } else {
            content.style.display = "none"; // Hide the content
        }
    }
</script>

<footer>
        <div class="bottom-bar">
            <div class="contact-info">
                <div class="email">Email: contact@example.com</div>
                <div class="address">Street Address: 123 Example St, City, Country</div>
            </div>
        </div>
    </footer>
</body>
</html>
