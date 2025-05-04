<?php
session_start();
if (isset($_GET['poll_id'], $_GET['token'], $_GET['poll']) && !empty($_GET['poll_id'])) {
  require("controllers/globalFunctions.php");
  $getPollInfo = getPollByID($conn, $_GET['poll_id'] ?? '');
  $getHostInfo = getHostInfo($conn, $getPollInfo['hostID'] ?? '');
  $invalidLink = false;
} else {
  $invalidLink = true;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="images/roe.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="NOINDEX,FOLLOW" />
  <title>Online Polling &amp; Voting System </title>
  <link rel="stylesheet" href="dist/vendors/homdroid/css/index.css" />
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
</head>

<body style="overflow-x: hidden;">
  <div id="root">
    <main>
      <div class="flex flex-col items-stretch _baseFullscreen_1tkux_22">
        <div id="navbar">
          <div class="_header_1r2x3_1 _white_1r2x3_33 _shadow_1r2x3_37">
            <div class="_headerImage_1r2x3_10"><img class=""
                src="images/roe.png" style="width: 5rem;height:5rem;"></div>
          </div>
        </div>
        <div class="_introSection_1tkux_30">
          <div class="_introSectionInnerContainer_1tkux_36 _innerContainer_1tkux_26">
            <p class="_introText_1tkux_40 text-base font-bold">Reohampton University</p>
            <p class="_introText_1tkux_40 mt-3 text-[2rem] font-bold md:mt-4.5 md:pb-1 md:text-5xl"><?= isset($getPollInfo['title']) && !($invalidLink) ? ucfirst($getPollInfo['title']) : 'Poll Not Found'; ?></p>
            <p class="_introText_1tkux_40 mt-2.5 text-lg md:mt-3 md:text-xl">Online Polling & Voting System, presented by Ridwan Olalekan Oguntola.</p>
          </div>
          <?php if (!($invalidLink)): ?>
            <div class="_contentSection_1tkux_50" style="min-height: 61vh;position: relative;">
              <div class="_contentSectionInnerContainer_1tkux_55 _innerContainer_1tkux_26">
                <div class="flex-grow">
                  <div>
                    <div>
                      <p class="mt-8 _subHeader2_kv9kd_19 font-bold">Things You Should Know Before Getting Started:</p>

                      <p class="mt-2">
                        1. <?= isset($getPollInfo['visibility']) && $getPollInfo['visibility'] == "private" ?
                              'This is a private poll, and only shortlisted email addresses are eligible to vote.' :
                              'This is a public poll, and you are eligible to participate only once.'; ?>
                      </p>

                      <p class="mt-2">2. Please enter your email address to begin the poll process.</p>

                      <p class="mt-2">3. Be sure to submit your vote before the poll ends, as only submitted votes are recorded.</p>

                      <p class="mt-2">4. You can return to submit your vote at any time, provided the poll is still active.</p>

                      <p class="mt-4 _subHeader2_kv9kd_19 font-bold">About the Poll</p>

                      <p class="mt-4">
                        This poll was created by <?= $getHostInfo['lname'] . " " . $getHostInfo['fname'] . " " . $getHostInfo['oname']; ?> <?= structureTimestamp($getPollInfo['createdAt']); ?>. It is available to
                        <?= isset($getPollInfo['visibility']) && $getPollInfo['visibility'] == "private" ?
                          'shortlisted voters who are registered to vote.' :
                          'everyone with a valid email address.'; ?>
                      </p>
                    </div>

                  </div>
                </div>
                <div class="_form_1tkux_69">
                  <form id="validateVoterPollEmail" class="mt-6">
                    <h2 class="mb-6 text-2xl font-bold">
                      <span id="verifyEmailLabel">Verify Your Email</span>
                      <span id="verifyOTPLabel" style="display: none;">Confirm OTP</span>
                      <span id="voterInfoFormLabel" style="display: none;">Enter Your Information</span>
                    </h2>

                    <!-- Email Verification Panel -->
                    <div id="verifyVoterEmail">
                      <div>
                        <label for="voterEmail">Email Address <span class="text-error">*</span></label>
                        <input name="voterEmail" id="voterEmail" type="email" placeholder="Please enter your valid email" class="input-bordered input w-full text-base text-black">
                      </div>
                      <div class="flex justify-center">
                        <button type="submit" id="verifyEmailBtn" class="btn mt-6 rounded-full normal-case">
                          Verify Email
                        </button>
                      </div>
                    </div>

                    <!-- OTP Verification Panel -->
                    <div id="verifyVoterEmailOtp" style="display: none;">
                      <div>
                        <label for="voterEmailOTP">Enter Six Digit OTP <span class="text-error">*</span></label>
                        <input name="voterEmailOTP" id="voterEmailOTP" type="text" placeholder="Please enter six digit number" class="input-bordered input w-full text-base text-black" maxlength="6">
                        <input type="hidden" name="pollID" id="pollID" value="<?= !empty($getPollInfo) ? $getPollInfo['pollID'] : ''; ?>">
                      </div>
                      <div id="verificationReset" class="mt-3 mb-2" style="display: none;">
                        <a href="javascript:void(0);" onclick="resetVerification();"><u>I did not get an Email!</u></a>
                      </div>
                      <div class="flex justify-center">
                        <button type="submit" id="verifyOtpBtn" class="btn mt-6 rounded-full normal-case">
                          Confirm OTP
                        </button>
                      </div>
                    </div>

                    <!-- Voter Information Panel -->
                    <div id="voterInfoForm" style="display: none;">
                      <div class="mb-3">
                        <label for="voterEmailForDisplay">Your Email Address</label>
                        <input id="voterEmailForDisplay" type="text" placeholder="Voter Email Address" class="input-bordered input w-full text-base text-black disabled" disabled>
                      </div>
                      <div class="mb-3">
                        <label for="voterFname">First Name <span class="text-error">*</span></label>
                        <input name="voterFname" id="voterFname" type="text" placeholder="Please enter your first name" class="input-bordered input w-full text-base text-black">
                      </div>
                      <div class="mt-3 ">
                        <label for="voterLname">Last Name <span class="text-error">*</span></label>
                        <input name="voterLname" id="voterLname" type="text" placeholder="Please enter your last name" class="input-bordered input w-full text-base text-black">
                      </div>
                      <div class="mt-3 ">
                        <label for="voterLname">Gender <span class="text-error">*</span></label>
                        <select name="voterGender" id="voterGender" class="input-bordered input w-full text-base text-black">
                          <option value="" disabled selected="">Select Your Gender</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                        </select>
                      </div>
                      <div class="flex flex-col items-center space-y-4">
                        <button type="submit" id="startPoll" class="btn mt-6 rounded-full normal-case">
                          Start Poll
                        </button>

                        <div class="mt-3">
                          <a href="javascript:void(0);" onclick="resetVerification();" class="text-blue-500 hover:underline">
                            <u>Wrong Email Address? Verify Another One.</u>
                          </a>
                        </div>
                      </div>

                    </div>
                  </form>
                  <!-- <form>
                  <h2 class="mb-6 text-2xl font-bold">Validate Your Email</h2>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1">
                        <input name="voterPollEmail" id="voterPollEmail" aria-labelledby="label-0"
                          type="text" class="_input_1odv5_32 input-bordered input w-full text-base text-black"
                          value=""><label class="_label_1odv5_6" for="input-0" id="label-0"><span
                            class="_labelSpan_1odv5_25 text-grey">Email Address</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label>
                      </div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1"><input name="familyName" id="input-1" aria-labelledby="label-1"
                          type="text" class="_input_1odv5_32 input-bordered input w-full text-base text-black"
                          value=""><label class="_label_1odv5_6" for="input-1" id="label-1"><span
                            class="_labelSpan_1odv5_25 text-grey">Last
                            name</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label></div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1"><input name="email" id="input-2" aria-labelledby="label-2" type="text"
                          class="_input_1odv5_32 input-bordered input w-full text-base text-black" value=""><label
                          class="_label_1odv5_6" for="input-2" id="label-2"><span
                            class="_labelSpan_1odv5_25 text-grey">Email</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label></div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="_base_rhddv_1" data-value="NG">
                      <div class="_selectContainer_rhddv_10"><select id="select-3" name="countryCode"
                          class="_select_rhddv_6 select-bordered select w-full text-base font-normal"
                          aria-labelledby="select-3" placeholder="Country of residence">
                          <option disabled="" value="">Country of residence</option>
                          <option class="_option_rhddv_44" value="AF">Afghanistan</option>
                          <option class="_option_rhddv_44" value="AX">Åland Islands</option>
                          <option class="_option_rhddv_44" value="AL">Albania</option>
                          <option class="_option_rhddv_44" value="DZ">Algeria</option>
                          <option class="_option_rhddv_44" value="AS">American Samoa</option>
                          <option class="_option_rhddv_44" value="AD">Andorra</option>
                          <option class="_option_rhddv_44" value="AO">Angola</option>
                          <option class="_option_rhddv_44" value="AI">Anguilla</option>
                          <option class="_option_rhddv_44" value="AQ">Antarctica</option>
                          <option class="_option_rhddv_44" value="AG">Antigua and Barbuda</option>
                          <option class="_option_rhddv_44" value="AR">Argentina</option>
                          <option class="_option_rhddv_44" value="AM">Armenia</option>
                          <option class="_option_rhddv_44" value="AW">Aruba</option>
                          <option class="_option_rhddv_44" value="AU">Australia</option>
                          <option class="_option_rhddv_44" value="AT">Austria</option>
                          <option class="_option_rhddv_44" value="AZ">Azerbaijan</option>
                          <option class="_option_rhddv_44" value="BS">Bahamas</option>
                          <option class="_option_rhddv_44" value="BH">Bahrain</option>
                          <option class="_option_rhddv_44" value="BD">Bangladesh</option>
                          <option class="_option_rhddv_44" value="BB">Barbados</option>
                          <option class="_option_rhddv_44" value="BY">Belarus</option>
                          <option class="_option_rhddv_44" value="BE">Belgium</option>
                          <option class="_option_rhddv_44" value="BZ">Belize</option>
                          <option class="_option_rhddv_44" value="BJ">Benin</option>
                          <option class="_option_rhddv_44" value="BM">Bermuda</option>
                          <option class="_option_rhddv_44" value="BT">Bhutan</option>
                          <option class="_option_rhddv_44" value="BO">Bolivia, Plurinational State of</option>
                          <option class="_option_rhddv_44" value="BQ">Bonaire, Sint Eustatius and Saba</option>
                          <option class="_option_rhddv_44" value="BA">Bosnia and Herzegovina</option>
                          <option class="_option_rhddv_44" value="BW">Botswana</option>
                          <option class="_option_rhddv_44" value="BV">Bouvet Island</option>
                          <option class="_option_rhddv_44" value="BR">Brazil</option>
                          <option class="_option_rhddv_44" value="IO">British Indian Ocean Territory</option>
                          <option class="_option_rhddv_44" value="BN">Brunei Darussalam</option>
                          <option class="_option_rhddv_44" value="BG">Bulgaria</option>
                          <option class="_option_rhddv_44" value="BF">Burkina Faso</option>
                          <option class="_option_rhddv_44" value="BI">Burundi</option>
                          <option class="_option_rhddv_44" value="KH">Cambodia</option>
                          <option class="_option_rhddv_44" value="CM">Cameroon</option>
                          <option class="_option_rhddv_44" value="CA">Canada</option>
                          <option class="_option_rhddv_44" value="CV">Cabo Verde</option>
                          <option class="_option_rhddv_44" value="KY">Cayman Islands</option>
                          <option class="_option_rhddv_44" value="CF">Central African Republic</option>
                          <option class="_option_rhddv_44" value="TD">Chad</option>
                          <option class="_option_rhddv_44" value="CL">Chile</option>
                          <option class="_option_rhddv_44" value="CN">China</option>
                          <option class="_option_rhddv_44" value="CX">Christmas Island</option>
                          <option class="_option_rhddv_44" value="CC">Cocos (Keeling) Islands</option>
                          <option class="_option_rhddv_44" value="CO">Colombia</option>
                          <option class="_option_rhddv_44" value="KM">Comoros</option>
                          <option class="_option_rhddv_44" value="CG">Congo</option>
                          <option class="_option_rhddv_44" value="CD">Congo, the Democratic Republic of the</option>
                          <option class="_option_rhddv_44" value="CK">Cook Islands</option>
                          <option class="_option_rhddv_44" value="CR">Costa Rica</option>
                          <option class="_option_rhddv_44" value="CI">Côte d'Ivoire</option>
                          <option class="_option_rhddv_44" value="HR">Croatia</option>
                          <option class="_option_rhddv_44" value="CU">Cuba</option>
                          <option class="_option_rhddv_44" value="CW">Curaçao</option>
                          <option class="_option_rhddv_44" value="CY">Cyprus</option>
                          <option class="_option_rhddv_44" value="CZ">Czech Republic</option>
                          <option class="_option_rhddv_44" value="DK">Denmark</option>
                          <option class="_option_rhddv_44" value="DJ">Djibouti</option>
                          <option class="_option_rhddv_44" value="DM">Dominica</option>
                          <option class="_option_rhddv_44" value="DO">Dominican Republic</option>
                          <option class="_option_rhddv_44" value="EC">Ecuador</option>
                          <option class="_option_rhddv_44" value="EG">Egypt</option>
                          <option class="_option_rhddv_44" value="SV">El Salvador</option>
                          <option class="_option_rhddv_44" value="GQ">Equatorial Guinea</option>
                          <option class="_option_rhddv_44" value="ER">Eritrea</option>
                          <option class="_option_rhddv_44" value="EE">Estonia</option>
                          <option class="_option_rhddv_44" value="ET">Ethiopia</option>
                          <option class="_option_rhddv_44" value="FK">Falkland Islands (Malvinas)</option>
                          <option class="_option_rhddv_44" value="FO">Faroe Islands</option>
                          <option class="_option_rhddv_44" value="FJ">Fiji</option>
                          <option class="_option_rhddv_44" value="FI">Finland</option>
                          <option class="_option_rhddv_44" value="FR">France</option>
                          <option class="_option_rhddv_44" value="GF">French Guiana</option>
                          <option class="_option_rhddv_44" value="PF">French Polynesia</option>
                          <option class="_option_rhddv_44" value="TF">French Southern Territories</option>
                          <option class="_option_rhddv_44" value="GA">Gabon</option>
                          <option class="_option_rhddv_44" value="GM">Gambia</option>
                          <option class="_option_rhddv_44" value="GE">Georgia</option>
                          <option class="_option_rhddv_44" value="DE">Germany</option>
                          <option class="_option_rhddv_44" value="GH">Ghana</option>
                          <option class="_option_rhddv_44" value="GI">Gibraltar</option>
                          <option class="_option_rhddv_44" value="GR">Greece</option>
                          <option class="_option_rhddv_44" value="GL">Greenland</option>
                          <option class="_option_rhddv_44" value="GD">Grenada</option>
                          <option class="_option_rhddv_44" value="GP">Guadeloupe</option>
                          <option class="_option_rhddv_44" value="GU">Guam</option>
                          <option class="_option_rhddv_44" value="GT">Guatemala</option>
                          <option class="_option_rhddv_44" value="GG">Guernsey</option>
                          <option class="_option_rhddv_44" value="GN">Guinea</option>
                          <option class="_option_rhddv_44" value="GW">Guinea-Bissau</option>
                          <option class="_option_rhddv_44" value="GY">Guyana</option>
                          <option class="_option_rhddv_44" value="HT">Haiti</option>
                          <option class="_option_rhddv_44" value="HM">Heard Island and McDonald Islands</option>
                          <option class="_option_rhddv_44" value="VA">Holy See (Vatican City State)</option>
                          <option class="_option_rhddv_44" value="HN">Honduras</option>
                          <option class="_option_rhddv_44" value="HK">Hong Kong</option>
                          <option class="_option_rhddv_44" value="HU">Hungary</option>
                          <option class="_option_rhddv_44" value="IS">Iceland</option>
                          <option class="_option_rhddv_44" value="IN">India</option>
                          <option class="_option_rhddv_44" value="ID">Indonesia</option>
                          <option class="_option_rhddv_44" value="IR">Iran, Islamic Republic of</option>
                          <option class="_option_rhddv_44" value="IQ">Iraq</option>
                          <option class="_option_rhddv_44" value="IE">Ireland</option>
                          <option class="_option_rhddv_44" value="IM">Isle of Man</option>
                          <option class="_option_rhddv_44" value="IL">Israel</option>
                          <option class="_option_rhddv_44" value="IT">Italy</option>
                          <option class="_option_rhddv_44" value="JM">Jamaica</option>
                          <option class="_option_rhddv_44" value="JP">Japan</option>
                          <option class="_option_rhddv_44" value="JE">Jersey</option>
                          <option class="_option_rhddv_44" value="JO">Jordan</option>
                          <option class="_option_rhddv_44" value="KZ">Kazakhstan</option>
                          <option class="_option_rhddv_44" value="KE">Kenya</option>
                          <option class="_option_rhddv_44" value="KI">Kiribati</option>
                          <option class="_option_rhddv_44" value="KP">Korea, Democratic People's Republic of</option>
                          <option class="_option_rhddv_44" value="KR">Korea, Republic of</option>
                          <option class="_option_rhddv_44" value="KW">Kuwait</option>
                          <option class="_option_rhddv_44" value="KG">Kyrgyzstan</option>
                          <option class="_option_rhddv_44" value="LA">Lao People's Democratic Republic</option>
                          <option class="_option_rhddv_44" value="LV">Latvia</option>
                          <option class="_option_rhddv_44" value="LB">Lebanon</option>
                          <option class="_option_rhddv_44" value="LS">Lesotho</option>
                          <option class="_option_rhddv_44" value="LR">Liberia</option>
                          <option class="_option_rhddv_44" value="LY">Libya</option>
                          <option class="_option_rhddv_44" value="LI">Liechtenstein</option>
                          <option class="_option_rhddv_44" value="LT">Lithuania</option>
                          <option class="_option_rhddv_44" value="LU">Luxembourg</option>
                          <option class="_option_rhddv_44" value="MO">Macau</option>
                          <option class="_option_rhddv_44" value="MK">Macedonia, the former Yugoslav Republic of</option>
                          <option class="_option_rhddv_44" value="MG">Madagascar</option>
                          <option class="_option_rhddv_44" value="MW">Malawi</option>
                          <option class="_option_rhddv_44" value="MY">Malaysia</option>
                          <option class="_option_rhddv_44" value="MV">Maldives</option>
                          <option class="_option_rhddv_44" value="ML">Mali</option>
                          <option class="_option_rhddv_44" value="MT">Malta</option>
                          <option class="_option_rhddv_44" value="MH">Marshall Islands</option>
                          <option class="_option_rhddv_44" value="MQ">Martinique</option>
                          <option class="_option_rhddv_44" value="MR">Mauritania</option>
                          <option class="_option_rhddv_44" value="MU">Mauritius</option>
                          <option class="_option_rhddv_44" value="YT">Mayotte</option>
                          <option class="_option_rhddv_44" value="MX">Mexico</option>
                          <option class="_option_rhddv_44" value="FM">Micronesia, Federated States of</option>
                          <option class="_option_rhddv_44" value="MD">Moldova, Republic of</option>
                          <option class="_option_rhddv_44" value="MC">Monaco</option>
                          <option class="_option_rhddv_44" value="MN">Mongolia</option>
                          <option class="_option_rhddv_44" value="ME">Montenegro</option>
                          <option class="_option_rhddv_44" value="MS">Montserrat</option>
                          <option class="_option_rhddv_44" value="MA">Morocco</option>
                          <option class="_option_rhddv_44" value="MZ">Mozambique</option>
                          <option class="_option_rhddv_44" value="MM">Myanmar</option>
                          <option class="_option_rhddv_44" value="NA">Namibia</option>
                          <option class="_option_rhddv_44" value="NR">Nauru</option>
                          <option class="_option_rhddv_44" value="NP">Nepal</option>
                          <option class="_option_rhddv_44" value="NL">Netherlands</option>
                          <option class="_option_rhddv_44" value="NC">New Caledonia</option>
                          <option class="_option_rhddv_44" value="NZ">New Zealand</option>
                          <option class="_option_rhddv_44" value="NI">Nicaragua</option>
                          <option class="_option_rhddv_44" value="NE">Niger</option>
                          <option class="_option_rhddv_44" value="NG">Nigeria</option>
                          <option class="_option_rhddv_44" value="NU">Niue</option>
                          <option class="_option_rhddv_44" value="NF">Norfolk Island</option>
                          <option class="_option_rhddv_44" value="MP">Northern Mariana Islands</option>
                          <option class="_option_rhddv_44" value="NO">Norway</option>
                          <option class="_option_rhddv_44" value="OM">Oman</option>
                          <option class="_option_rhddv_44" value="PK">Pakistan</option>
                          <option class="_option_rhddv_44" value="PW">Palau</option>
                          <option class="_option_rhddv_44" value="PS">Palestine, State of</option>
                          <option class="_option_rhddv_44" value="PA">Panama</option>
                          <option class="_option_rhddv_44" value="PG">Papua New Guinea</option>
                          <option class="_option_rhddv_44" value="PY">Paraguay</option>
                          <option class="_option_rhddv_44" value="PE">Peru</option>
                          <option class="_option_rhddv_44" value="PH">Philippines</option>
                          <option class="_option_rhddv_44" value="PN">Pitcairn</option>
                          <option class="_option_rhddv_44" value="PL">Poland</option>
                          <option class="_option_rhddv_44" value="PT">Portugal</option>
                          <option class="_option_rhddv_44" value="PR">Puerto Rico</option>
                          <option class="_option_rhddv_44" value="QA">Qatar</option>
                          <option class="_option_rhddv_44" value="RE">Réunion</option>
                          <option class="_option_rhddv_44" value="RO">Romania</option>
                          <option class="_option_rhddv_44" value="RU">Russian Federation</option>
                          <option class="_option_rhddv_44" value="RW">Rwanda</option>
                          <option class="_option_rhddv_44" value="BL">Saint Barthélemy</option>
                          <option class="_option_rhddv_44" value="SH">Saint Helena, Ascension and Tristan da Cunha
                          </option>
                          <option class="_option_rhddv_44" value="KN">Saint Kitts and Nevis</option>
                          <option class="_option_rhddv_44" value="LC">Saint Lucia</option>
                          <option class="_option_rhddv_44" value="MF">Saint Martin (French part)</option>
                          <option class="_option_rhddv_44" value="PM">Saint Pierre and Miquelon</option>
                          <option class="_option_rhddv_44" value="VC">Saint Vincent and the Grenadines</option>
                          <option class="_option_rhddv_44" value="WS">Samoa</option>
                          <option class="_option_rhddv_44" value="SM">San Marino</option>
                          <option class="_option_rhddv_44" value="ST">Sao Tome and Principe</option>
                          <option class="_option_rhddv_44" value="SA">Saudi Arabia</option>
                          <option class="_option_rhddv_44" value="SN">Senegal</option>
                          <option class="_option_rhddv_44" value="RS">Serbia</option>
                          <option class="_option_rhddv_44" value="SC">Seychelles</option>
                          <option class="_option_rhddv_44" value="SL">Sierra Leone</option>
                          <option class="_option_rhddv_44" value="SG">Singapore</option>
                          <option class="_option_rhddv_44" value="SX">Sint Maarten (Dutch part)</option>
                          <option class="_option_rhddv_44" value="SK">Slovakia</option>
                          <option class="_option_rhddv_44" value="SI">Slovenia</option>
                          <option class="_option_rhddv_44" value="SB">Solomon Islands</option>
                          <option class="_option_rhddv_44" value="SO">Somalia</option>
                          <option class="_option_rhddv_44" value="ZA">South Africa</option>
                          <option class="_option_rhddv_44" value="GS">South Georgia and the South Sandwich Islands
                          </option>
                          <option class="_option_rhddv_44" value="SS">South Sudan</option>
                          <option class="_option_rhddv_44" value="ES">Spain</option>
                          <option class="_option_rhddv_44" value="LK">Sri Lanka</option>
                          <option class="_option_rhddv_44" value="SD">Sudan</option>
                          <option class="_option_rhddv_44" value="SR">Suriname</option>
                          <option class="_option_rhddv_44" value="SJ">Svalbard and Jan Mayen</option>
                          <option class="_option_rhddv_44" value="SZ">Swaziland</option>
                          <option class="_option_rhddv_44" value="SE">Sweden</option>
                          <option class="_option_rhddv_44" value="CH">Switzerland</option>
                          <option class="_option_rhddv_44" value="SY">Syrian Arab Republic</option>
                          <option class="_option_rhddv_44" value="TW">Taiwan</option>
                          <option class="_option_rhddv_44" value="TJ">Tajikistan</option>
                          <option class="_option_rhddv_44" value="TZ">Tanzania, United Republic of</option>
                          <option class="_option_rhddv_44" value="TH">Thailand</option>
                          <option class="_option_rhddv_44" value="TL">Timor-Leste</option>
                          <option class="_option_rhddv_44" value="TG">Togo</option>
                          <option class="_option_rhddv_44" value="TK">Tokelau</option>
                          <option class="_option_rhddv_44" value="TO">Tonga</option>
                          <option class="_option_rhddv_44" value="TT">Trinidad and Tobago</option>
                          <option class="_option_rhddv_44" value="TN">Tunisia</option>
                          <option class="_option_rhddv_44" value="TR">Turkey</option>
                          <option class="_option_rhddv_44" value="TM">Turkmenistan</option>
                          <option class="_option_rhddv_44" value="TC">Turks and Caicos Islands</option>
                          <option class="_option_rhddv_44" value="TV">Tuvalu</option>
                          <option class="_option_rhddv_44" value="UG">Uganda</option>
                          <option class="_option_rhddv_44" value="UA">Ukraine</option>
                          <option class="_option_rhddv_44" value="AE">United Arab Emirates</option>
                          <option class="_option_rhddv_44" value="GB">United Kingdom</option>
                          <option class="_option_rhddv_44" value="US">United States</option>
                          <option class="_option_rhddv_44" value="UM">United States Minor Outlying Islands</option>
                          <option class="_option_rhddv_44" value="UY">Uruguay</option>
                          <option class="_option_rhddv_44" value="UZ">Uzbekistan</option>
                          <option class="_option_rhddv_44" value="VU">Vanuatu</option>
                          <option class="_option_rhddv_44" value="VE">Venezuela, Bolivarian Republic of</option>
                          <option class="_option_rhddv_44" value="VN">Viet Nam</option>
                          <option class="_option_rhddv_44" value="VG">Virgin Islands, British</option>
                          <option class="_option_rhddv_44" value="VI">Virgin Islands, U.S.</option>
                          <option class="_option_rhddv_44" value="WF">Wallis and Futuna</option>
                          <option class="_option_rhddv_44" value="EH">Western Sahara</option>
                          <option class="_option_rhddv_44" value="YE">Yemen</option>
                          <option class="_option_rhddv_44" value="ZM">Zambia</option>
                          <option class="_option_rhddv_44" value="ZW">Zimbabwe</option>
                        </select></div><label class="_label_rhddv_48" for="input-3" id="select-3"><span
                          class="_labelSpan_rhddv_63 text-grey">Country of residence</span>
                        <div class="ml-0.5 text-red-500">*</div>
                      </label>
                    </div>
                    <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                  </div>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1"><input name="city" id="input-4" aria-labelledby="label-4" type="text"
                          class="_input_1odv5_32 input-bordered input w-full text-base text-black" value=""><label
                          class="_label_1odv5_6" for="input-4" id="label-4"><span
                            class="_labelSpan_1odv5_25 text-grey">City</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label></div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1"><input name="birthYear" id="input-5" aria-labelledby="label-5"
                          type="text" class="_input_1odv5_32 input-bordered input w-full text-base text-black"
                          value=""><label class="_label_1odv5_6" for="input-5" id="label-5"><span
                            class="_labelSpan_1odv5_25 text-grey">Year
                            of birth (YYYY)</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label></div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="_base_rhddv_1" data-value="">
                      <div class="_selectContainer_rhddv_10"><select id="select-6" name="gender"
                          class="_select_rhddv_6 select-bordered select w-full text-base font-normal"
                          aria-labelledby="select-6" placeholder="Gender">
                          <option disabled="" value="">Gender</option>
                          <option class="_option_rhddv_44" value="female">Female</option>
                          <option class="_option_rhddv_44" value="male">Male</option>
                          <option class="_option_rhddv_44" value="other">Other</option>
                        </select></div><label class="_label_rhddv_48" for="input-6" id="select-6"><span
                          class="_labelSpan_rhddv_63 text-grey">Gender</span>
                        <div class="ml-0.5 text-red-500">*</div>
                      </label>
                    </div>
                    <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                  </div>
                  <div class="mb-3">
                    <div>
                      <div class="_base_1odv5_1"><input name="participantId" id="input-7" aria-labelledby="label-7"
                          type="text" class="_input_1odv5_32 input-bordered input w-full text-base text-black"
                          value=""><label class="_label_1odv5_6" for="input-7" id="label-7"><span
                            class="_labelSpan_1odv5_25 text-grey">Application ID</span>
                          <div class="ml-0.5 text-error">*</div>
                        </label></div>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <hr class="my-6">
                  <div class="form-control"><label class="label cursor-pointer items-start justify-start"><input
                        type="checkbox" name="subscribe"
                        class="checkbox mr-2 disabled:border-gray-300 disabled:bg-transparent disabled:opacity-100"
                        value=""><span class="label-text">Yes, I would like to receive information about EF's English
                        learning programs.</span></label></div>
                  <div class="form-control mt-2 max-w-md">
                    <div><label class="label cursor-pointer items-start justify-start"><input type="checkbox"
                          name="acceptedTerms"
                          class="checkbox mr-2 disabled:border-gray-300 disabled:bg-transparent disabled:opacity-100"
                          value=""><span class="label-text"><span class="mr-0.5 text-error">*</span>Yes, I (or my legal
                          guardian) have read and understood how EF processes my personal data as set out in the<span>
                          </span><a class="mt-2 underline" href="https://www.ef.com/legal/privacy-policy/" target="_blank"
                            rel="noreferrer">Privacy Policy</a></span></label>
                      <div class="mt-0.5 text-left text-sm font-bold text-error"></div>
                    </div>
                  </div>
                  <div class="flex justify-center"><button
                      class="_base_1awbz_1 btn mt-6 rounded-full _gradient_1awbz_10 normal-case">
                      <div>Start test</div>
                    </button></div>
                </form> -->
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
    </main>
  </div>
</body>

<script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
<script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
<script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
<script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Scripts -->
<script src="js/start-poll.js"></script>

</html>