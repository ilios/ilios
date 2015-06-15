<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 26390,
            'title' => "'Internal Organizaiton of the Cell'",
            'description' => "An advanced text that correlates cell structure with function at a<br>molecular level",
            'uploadDate' => "2014-08-22T17:22:39+00:00",
            'originalAuthor' => "Morgan, Kathleen",
            'token' => "9090d3eee33078ceb70c6800bf67d2a0c5555f229c8b656a70b3ba2df9fca0b5",
            'userRole' => "1",
            'status' => "1",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Molecular Biology of the Cell, Fifth Edition, Part III, “Internal Organization of the Cell” ",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26416,
            'title' => "Basica & Clinical Pharmacology",
            'description' => "This is a good chance for you to become accustomed to using the textbook to supplement the syllabus. The textbook is especially useful for providing an overview and for more in-depth explanations.",
            'uploadDate' => "2014-08-22T19:07:24+00:00",
            'originalAuthor' => "Chapman, Kimberly",
            'token' => "8a11a0a11aee33ff5a2075e6244528f34213320c91512e7ae1f65a7e702f3dab",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Chapters 7 and 8 in Basic & Clinical Pharmacology, 12th edition discuss these two drug groups. ",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26424,
            'title' => "Wheater's Functional Histology",
            'description' => "Recommended Reading",
            'uploadDate' => "2014-08-22T19:29:34+00:00",
            'originalAuthor' => "Ryan, Jack",
            'token' => "401b942dfb64b5fceddf7d0da6c2d6522dd4017c49e5c541af5f59c5f004a944",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => " Wheater's Functional Histology (a general reference text book) ",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26455,
            'title' => "Molecular regulation",
            'description' => "Required reading",
            'uploadDate' => "2014-08-22T20:22:27+00:00",
            'originalAuthor' => "Johnston, Norma",
            'token' => "c9ae7cee8de2df1da1fda735059e9296fed50768d883b783933f9328412b6e82",
            'userRole' => "3",
            'status' => "1",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Langman’s Medical Embryology 12th edition Review pp. 70-74 section beginning with “Molecular regulation…” is optional (pp. 74-79)",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26456,
            'title' => "Langman's Medical Embryology ",
            'description' => "Required reading",
            'uploadDate' => "2014-08-22T20:23:39+00:00",
            'originalAuthor' => "Parker, Shawn",
            'token' => "6527bf1354e470831ff862ecb28b9689214ecdea32b9ebd095c791e4c8d477fc",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Langman’s Medical Embryology 12th edition “Vertebrae and the vertebral column” and “Ribs and sternum” pp. 142-144 “Limbs” pp. 151-157",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26457,
            'title' => "Molecular regulation of limb development",
            'description' => "Required reading",
            'uploadDate' => "2014-08-22T20:24:12+00:00",
            'originalAuthor' => "Lopez, Douglas",
            'token' => "5736dda53308bab2773a5fa5d00dee52d24b49c1f9396e7e9e4652b25a17b432",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "“Molecular regulation of limb development” section has more detail than is required, pp. 134-134, 147-151",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26458,
            'title' => "Resegmentation 'Vertebrae'",
            'description' => "Resegmentation-http://embryology.med.unsw.edu.au/Movies/mesoderm.htm (scroll down to “Vertebrae”)",
            'uploadDate' => "2014-08-22T20:25:35+00:00",
            'originalAuthor' => "Scott, Barbara",
            'token' => "8d1b5acbcbc9aaac048e456a14bac2b9d09618fb0850a5bd0a7d1e5d83fd033b",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://embryology.med.unsw.edu.au/Movies/mesoderm.htm",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26459,
            'title' => "Simbryo",
            'description' => "Limb axes and rotation- Video on limbs in “Simbryo” on the CLE http://missinglink.ucsf.edu/restricted/simbryo/site/index.html",
            'uploadDate' => "2014-08-22T20:26:16+00:00",
            'originalAuthor' => "West, Timothy",
            'token' => "1f53ee53807ca9b1d08959a4fa62341098d3f8a5c4d6043704b25bb0e380cc53",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://missinglink.ucsf.edu/restricted/simbryo/site/index.html",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26515,
            'title' => "Prologue Course Syllabus 2014",
            'description' => "Entire course syllabus in PDF",
            'uploadDate' => "2014-08-26T15:54:09+00:00",
            'originalAuthor' => "Russell, Theresa",
            'token' => "5317c46f997cb2b4327c74e046cb3175185e40db10746d435274139e959e80bf",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => ['1247'],
            'path' => "/learning_materials/595/0/20140826-085409_319_d06bdedec25c6ab8a0328b0357203ee3",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 23737,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26544,
            'title' => "The Components and Properties of Cell Membranes ILM",
            'description' => "The Components and Properties of Cell Membranes ILM on iROCKET and syllabus",
            'uploadDate' => "2014-08-27T17:23:19+00:00",
            'originalAuthor' => "Wells, Keith",
            'token' => "bed8eaae37d3df6f9c1479e15bb23e74abc0b7a99ec2b2f63bffc9ed14de3951",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "https://courses.ucsf.edu/pluginfile.php/186776/mod_page/content/10/PROF14_ComponentsPropsCellMembrane_ILM.pdf",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26550,
            'title' => "Medical Physiology",
            'description' => "(This section is detailed, but contains useful descriptions of nearly every type of transport protein you will encounter in other blocks.",
            'uploadDate' => "2014-08-27T18:44:11+00:00",
            'originalAuthor' => "Young, Margaret",
            'token' => "24b2342af76b3911cb2c8ada0efd1ccf1cb13cf70406756bf0c5029ea3837f6a",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Boron and Boulpaep, Medical Physiology, 2nd ed., Saunders, 2009, pp. 113-132.",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26551,
            'title' => "Essential Cell Biology",
            'description' => "Supplemental reading",
            'uploadDate' => "2014-08-27T18:44:34+00:00",
            'originalAuthor' => "Reyes, Michelle",
            'token' => "4ac0608f3b14ebf91f78c8f0e064185325f3c343933fb3a57bc040790d6064d0",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Alberts et al., Essential Cell Biology, 3rd ed.,Garland Publishing, 2010, pp. 372- 420.",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26557,
            'title' => "Basic & Clinical Pharmacology",
            'description' => "In Chapter 1, the sections that are most important include “The Nature of Drugs”, “Drug-Body Interactions”, “Pharmacodynamic Principles”, and “Pharmacokinetic Principles”. The topics of drug permeation and ionization of weak acids and weak bases, which are addressed in Chapter 1 of the textbook, will be covered by Dr. Fulton in lecture and you will work with this concept in your first small group. Dr. Fulton also will expand on drug reactivity and drug-receptor bonds.",
            'uploadDate' => "2014-08-27T18:56:11+00:00",
            'originalAuthor' => "Castillo, Harry",
            'token' => "2ea15f542850d151c46b875f147fe5439f0f12cd907033f09dcbe5b6574c6fa0",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Chapter 1, Basic and Clinical Pharmacology, 12th edition",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26558,
            'title' => "Basic & Clinical Pharmacology cont.",
            'description' => "Chapt 5 reading",
            'uploadDate' => "2014-08-27T18:56:53+00:00",
            'originalAuthor' => "Holmes, Marilyn",
            'token' => "f594ce40d3da9bbf3e7081eb3b9052404d5f33d47f94c4f2a4fe519027549c0a",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Chapter 5, Basic and Clinical Pharmacology, 12th edition. Read the first section on p. 69, and the section entitled “The Food & Drug Administration” on pp. 73-77. Look over Figure 5-1 and read through Table 5-2 on p. 74.",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26559,
            'title' => "Basic & Clinical Pharmacology cont. 2",
            'description' => "Chapt 65 reading",
            'uploadDate' => "2014-08-27T18:57:25+00:00",
            'originalAuthor' => "Castillo, Kathy",
            'token' => "650584a57382cf708f82f29c4c8973d8bf33ad075a477d1b7b57c4e49d76b8a7",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Chapter 65, Basic and Clinical Pharmacology, 12th edition. Read the sections “Legal Factors (USA)”, “Who May Prescribe”, and “Socioeconomic Factors” on pp. 1144-1148.",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26560,
            'title' => "Prilosec, Nexium & Stereoisomers",
            'description' => "Recommended reading. Available on iROCKET.",
            'uploadDate' => "2014-08-27T18:58:13+00:00",
            'originalAuthor' => "Graham, Julie",
            'token' => "530ebafa16c1ad03d868b42adb4aba5160d4392fe346f275a18dd88bc5e5d742",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => "Prilosec, Nexium and Stereoisomers. Med Lett Drugs Ther. 2003 Jun 23;45(1159):51-2.",
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 26718,
            'title' => "Cells & Tissues-Lecture Slides Ppt",
            'description' => "Lecture slides Ppt",
            'uploadDate' => "2014-09-05T20:28:28+00:00",
            'originalAuthor' => "Jones, Joan",
            'token' => "90d59f118722713b1d3dcff0ac50d6684fa5fc506afe49c04db17300683acccb",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-132828_529_889709e9cf5b198b806161ec3c7f6593",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.ppt",
            'mimetype' => "application/vnd.ms-powerpoint",
            'filesize' => 10330,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26719,
            'title' => "Cells & Tissues-Lecture Slides PDF",
            'description' => "Lecture slides PDF",
            'uploadDate' => "2014-09-05T20:30:40+00:00",
            'originalAuthor' => "Frazier, Marilyn",
            'token' => "c1c8b762d0a80ae20f7e7cbdd8f1809e20dbfa25a08f5b49c1a222e06c60b6ae",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-133040_107_3338b3e9cb5ae7cd694864791d46c199",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 9015,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26722,
            'title' => "Diffusion & Transport Across Cell Membrane-Lecture Slides Pp",
            'description' => "Lecture slides Ppt",
            'uploadDate' => "2014-09-05T20:38:05+00:00",
            'originalAuthor' => "Sanchez, Harold",
            'token' => "c83ec057f08015ed97b027c58d81bfe7a1a891ea1e8dc40196cf65bc42b75bb2",
            'userRole' => "2",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16480/20140905-133805_391_2caf560abd61649c43f983edc01dce86",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 317,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26723,
            'title' => "Diffusion & Transport Across Cell Membrane-Lecture Slides PD",
            'description' => "Lecture slides PDF",
            'uploadDate' => "2014-09-05T20:39:01+00:00",
            'originalAuthor' => "Myers, Ruby",
            'token' => "b966d9e3cce14722f98991083d79bf9bdeb730f9a03ed2316e041afd78072b4e",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16480/20140905-133901_513_c1e7bebadc54823ac07f099a187603d0",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 2965,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26730,
            'title' => "Cell Compartments-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:06:07+00:00",
            'originalAuthor' => "Phillips, John",
            'token' => "1df0c18b04489b748e09fe8fee4f7b896431b427eb10c9a7bb4538f6b09f35bf",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-140607_202_096a9af339b94281ebe383a78b6b5295",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 13807,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26731,
            'title' => "Chromosome Coil-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:06:51+00:00",
            'originalAuthor' => "Wallace, Joyce",
            'token' => "2fc529c8d142d4d050e74900592f0af68fe27a91181338ee02ab4e7350758281",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-140651_812_7888c19b554ffb8226c861ffdb0964cf",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 4106,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26732,
            'title' => "Mitochondrion-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:07:35+00:00",
            'originalAuthor' => "Kelly, Carlos",
            'token' => "5cf79ae3dcdd535ab69709f978ae3cf4a0215a9a80c740b194780dafdb6c61bc",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-140735_972_e04058a123fefc90482700ebf050f459",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 7720,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26733,
            'title' => "Mitotic Spindles-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:08:14+00:00",
            'originalAuthor' => "Boyd, Wanda",
            'token' => "f92c841c0ea9127975ff71b8908541b1b6c08623f302b97a298976c870a8dc3e",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-140814_167_f8d2e1817d384c7a2aaaaa9b6a6129da",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 5022,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26734,
            'title' => "Translation-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:08:48+00:00",
            'originalAuthor' => "Hicks, Ronald",
            'token' => "2dc9151df36834eeb6d1bee08b23a8d462d7436a0076a14e45258aa29340ab4e",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-140848_661_fd06dc3b3b466944fc9c5977c0eef1de",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 11073,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26735,
            'title' => "Sickle Cell-Video",
            'description' => "Video clip",
            'uploadDate' => "2014-09-05T21:13:15+00:00",
            'originalAuthor' => "Arnold, Johnny",
            'token' => "7065ba9f9fc040ed1571a4ab6ecab48b17fd4d8ad65083ea2b9eb939fc5ba9bb",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140905-141315_926_9118b8872f1f8003340c1b5ca98ae2d0",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 4677,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26752,
            'title' => "Just 13, and Working Risky 12-Hour Shifts in the Tobacco Fie",
            'description' => "A September 6, 2014 New York Times article about adolescents working in tobacco fields. Note the toxicity they experience from the excess nicotine they are exposed to. Fits with what we discussed in Prologue class.",
            'uploadDate' => "2014-09-07T15:01:17+00:00",
            'originalAuthor' => "Sims, Michael",
            'token' => "fcc94f247a3c08edfd59fa8424a37496b7d75edc08253ec021a7106b2d9704f6",
            'userRole' => "1",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://www.nytimes.com/2014/09/07/business/just-13-and-working-risky-12-hour-shifts-in-the-tobacco-fields.html?hp&action=click&pgtype=Homepage&version=HpSumSmallMediaHigh&module=second-column-region&region=top-news&WT.nav=top-news&_r=0",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26759,
            'title' => "Diffusion & Transport WORKSHEET (Due 9/9)",
            'description' => "<span style='font-size: medium;'>Prior to lecture, ensure you have  access to this worksheet either as a printed hard copy or the electronic  version. You will complete it during lecture.</span>",
            'uploadDate' => "2014-09-08T17:20:42+00:00",
            'originalAuthor' => "Spencer, Billy",
            'token' => "65c0844f6c1e2cc47db6544e8cc8b1d1762319776b080d436bffb1016b296d90",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16480/20140908-102042_995_ce3351df71b77a1f7102a075c5dfc8fc",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 148,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26801,
            'title' => "Cells & Tissues-AudioCast (MP3)",
            'description' => "MP3 audio recording of today's lecture",
            'uploadDate' => "2014-09-08T21:51:11+00:00",
            'originalAuthor' => "Payne, Randy",
            'token' => "a856d37a141615addb6088b9b7ebf16a9d82a1b3591ec5ea06acf71e992406b9",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16471/20140908-145111_824_55cd5661f57d38bcf7dc73d25d48da18",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.mp3",
            'mimetype' => "audio/mpeg",
            'filesize' => 23464,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26802,
            'title' => "Cells & Tissues-LectureCast",
            'description' => "Recording of today's lecture (video & Ppt)",
            'uploadDate' => "2014-09-08T21:52:00+00:00",
            'originalAuthor' => "Diaz, Kathryn",
            'token' => "8c3fb418f8d239c728933bc9e09202290cbbe81f7358f14c143c57c77d77243a",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://lecture.ucsf.edu/ets/Play/873e5821fd144b5490b341bc6e5fe9bf1d?catalog=efbe12ed-39f1-4fbe-b79e-6bd1ad364935",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26842,
            'title' => "Diffusion & Transport Across Cell Membranes-LectureCast",
            'description' => "Recording of today's lecture (video & Ppt)",
            'uploadDate' => "2014-09-09T21:45:08+00:00",
            'originalAuthor' => "Walker, Phillip",
            'token' => "032f0f716dbb5c103af9a865a76b7c9ce214162c3ee1cb12a717f5c1da6b6760",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://lecture.ucsf.edu/ets/Play/ae335a297b0641bbbcda15fd164a91581d?catalog=efbe12ed-39f1-4fbe-b79e-6bd1ad364935",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26843,
            'title' => "Diffusion & Transport Across Cell Membranes-LectureCast",
            'description' => "Recording of today's lecture (video & Ppt)",
            'uploadDate' => "2014-09-09T21:45:08+00:00",
            'originalAuthor' => "Nelson, Carlos",
            'token' => "bf0087b9bc5ae039ef0165135eeb2deeebb67b92e7a82a1f047ce2740bc4b4ee",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://lecture.ucsf.edu/ets/Play/ae335a297b0641bbbcda15fd164a91581d?catalog=efbe12ed-39f1-4fbe-b79e-6bd1ad364935",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26844,
            'title' => "Diffusion & Transport Across Cell Membranes-AudioCast",
            'description' => "MP3 audio recording of today's lecture",
            'uploadDate' => "2014-09-09T21:47:49+00:00",
            'originalAuthor' => "Carr, Joan",
            'token' => "17aa27a807728c013841ce0d8f529275944b00327d74a8e906bc80c1fa331291",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16480/20140909-144749_639_f5d0ac1395b0a8c48d094d89250395e7",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.mp3",
            'mimetype' => "audio/mpeg",
            'filesize' => 51573,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26848,
            'title' => "Diffusion & Transport Across Cell Membranes-COMPLETE Slides ",
            'description' => "Complete lecture slides w/Q&As and annotations",
            'uploadDate' => "2014-09-09T22:16:08+00:00",
            'originalAuthor' => "Long, Samuel",
            'token' => "9d1eb186655df7a9ad8c544e6afaec827c0befd2cb33125a3a02371700d30e18",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16480/20140909-151608_927_42fd7250ca1f45da157caa9988d6ef80",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 2580,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26929,
            'title' => "Micromedex database link",
            'description' => "Link to the library's Micromedex database",
            'uploadDate' => "2014-09-11T22:08:55+00:00",
            'originalAuthor' => "Turner, Bonnie",
            'token' => "31a3a02d311f8b3f983c70f2ea1bce3ccd7f9617925029e77213b52ce9992d14",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://www.library.ucsf.edu/db/micromedex-20",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26930,
            'title' => "Epocrates drug database link",
            'description' => "Epocrates drug database",
            'uploadDate' => "2014-09-11T22:11:16+00:00",
            'originalAuthor' => "Wells, Sean",
            'token' => "7d93222942806032a9728c049c64bb12fb506aa1dbade8b0b62ba51b8a420fa9",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "https://online.epocrates.com/",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 26937,
            'title' => "Intro to Pharmacology-Lecture Slides Ppt",
            'description' => "Lecture slides Ppt",
            'uploadDate' => "2014-09-11T22:37:02+00:00",
            'originalAuthor' => "Stewart, Stephen",
            'token' => "40759ed0ff238a549405740a06bd97d4a84227678855465ff6a46c3bd637cd21",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16473/20140911-153702_458_8af90cf7233b1177c1407eda89b42af7",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.ppt",
            'mimetype' => "application/vnd.ms-powerpoint",
            'filesize' => 2840,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26938,
            'title' => "Intro to Pharmacology-Lecture Slides PDF",
            'description' => "Lecture slides PDF",
            'uploadDate' => "2014-09-11T22:37:28+00:00",
            'originalAuthor' => "Day, Kimberly",
            'token' => "203a855bc9c92cda0f3ed68d4027cb48ba52fd464d70f4a9e862f8176211cdbd",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16473/20140911-153728_677_589d62caf0865b650ec9f4f7a581d480",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 2096,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26957,
            'title' => "Lecture slides ppt",
            'description' => "Lecture slides ppt",
            'uploadDate' => "2014-09-12T19:16:48+00:00",
            'originalAuthor' => "Martin, Johnny",
            'token' => "c698911f54dc0de8ea94827cdc00fa5ab96e34080bc41ef541a1607203490d48",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140912-121648_962_434cd1092187fb90022f2f37e6266395",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.ppt",
            'mimetype' => "application/vnd.ms-powerpoint",
            'filesize' => 1917,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26958,
            'title' => "Lecture slides pdf",
            'description' => "Lecture slides pdf",
            'uploadDate' => "2014-09-12T19:17:09+00:00",
            'originalAuthor' => "Thomas, Rebecca",
            'token' => "2f5c91211930b183a18550e3ba30c01dde37068c3816bdf670e60e8ed4031da5",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140912-121709_662_0e46cfcff06d8387415c004c0ef14fa3",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 1100,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26959,
            'title' => "how to study slides",
            'description' => "how to study slides",
            'uploadDate' => "2014-09-12T19:19:15+00:00",
            'originalAuthor' => "Ellis, Betty",
            'token' => "f2e3e933065e035e263d0c48728a21e1c4f148a243fc38d2fba39c321fc5070a",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140912-121915_255_b08aa1d1fe54757b37b36554b787da83",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 1001,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26960,
            'title' => "how to study slides",
            'description' => "how to study slides2",
            'uploadDate' => "2014-09-12T19:19:40+00:00",
            'originalAuthor' => "Sims, Billy",
            'token' => "5535471efc40afe2a95a20959f849cfba5880d054f28d8503f035a75831179c4",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140912-121940_995_2409112d138cec1ffd45467e004bda78",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 718,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26962,
            'title' => "Intro to Pharmacology-LectureCast",
            'description' => "MP3 audio recording of this lecture",
            'uploadDate' => "2014-09-12T21:04:17+00:00",
            'originalAuthor' => "Martinez, Jesse",
            'token' => "656bdd68bc27ff096849539a86ae245d231deaa35e892b4c96da1d1c8acbe559",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16473/20140912-140417_884_33b2cbffa4bef868c5038163a35b4bf5",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.mp3",
            'mimetype' => "audio/mpeg",
            'filesize' => 23465,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 26963,
            'title' => "Intro to Pharmacology-AudioCast (MP3)",
            'description' => "Recording of this lecture (video & Ppt)",
            'uploadDate' => "2014-09-12T21:06:56+00:00",
            'originalAuthor' => "Fisher, Joyce",
            'token' => "4616ccfcea9a6f7f5cf355d55ebe2dffeddef99657fdc13229fee95029f338e1",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://lecture.ucsf.edu/ets/Play/d8dd0ad52cee413e8fa4ca7f2282025a1d?catalog=efbe12ed-39f1-4fbe-b79e-6bd1ad364935",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 27037,
            'title' => "transcripts",
            'description' => "transcripts, zip",
            'uploadDate' => "2014-09-16T18:27:33+00:00",
            'originalAuthor' => "Williamson, Lois",
            'token' => "5c6a831b600fc7b588aed8292276dab34a387ff856b94c4bad89f6326c8db9a4",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140916-112733_806_1c672e263fa873931c4f71e63eeeec95",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 14,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 27038,
            'title' => "Online Lecture",
            'description' => "Online Lecture<span class='Apple-tab-span' style='white-space:pre'>		</span>",
            'uploadDate' => "2014-09-16T18:28:09+00:00",
            'originalAuthor' => "Mendoza, Pamela",
            'token' => "f14caf657d1fc90f29f8818e058be287239b43b031b407bb397fadd91ed93979",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "https://courses.ucsf.edu/mod/page/view.php?id=111731",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 27039,
            'title' => "How to study pharmacology",
            'description' => "How to Study Pharmacology",
            'uploadDate' => "2014-09-16T18:33:29+00:00",
            'originalAuthor' => "Smith, Patrick",
            'token' => "b5faeb7f32f85f468b88aec7e6558fcc14b471ed874f8b08806ab79ca2ed4850",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "https://courses.ucsf.edu/mod/page/view.php?id=111736",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 27040,
            'title' => "transcript, how to study",
            'description' => "transcript, how to study",
            'uploadDate' => "2014-09-16T18:34:15+00:00",
            'originalAuthor' => "Robertson, Fred",
            'token' => "bf0d70bcc1be5dfe60f7e5b8160115c2ce4fab38a77ba9832c06d790e6030e02",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16491/20140916-113415_855_521f08a71290b0d7fa3f664867080c29",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.txt",
            'mimetype' => "text/plain",
            'filesize' => 11,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 28030,
            'title' => "Development of Musculoskeletal and Integumentary Systems-Lec",
            'description' => "Lecture slides in Ppt",
            'uploadDate' => "2014-10-06T16:12:42+00:00",
            'originalAuthor' => "Hughes, Keith",
            'token' => "653523cb5ba14efeaf98df267b00e7b35db47e4a13e7e3c59f45ef811b391a48",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16556/20141006-091242_488_2a1dd334d84b2d6b8cf5dd3cb9bec55e",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.ppt",
            'mimetype' => "application/vnd.ms-powerpoint",
            'filesize' => 13285,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 28031,
            'title' => "Development of Musculoskeletal and Integumentary Systems-Lec",
            'description' => "Lecture slides  PDF",
            'uploadDate' => "2014-10-06T16:13:24+00:00",
            'originalAuthor' => "Perez, Juan",
            'token' => "497bcc2c317caa997eacb4a3db228390e84104ac1da992347fbf1d0292c41aab",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16556/20141006-091324_353_d5a320f864d0061bb243c398f706f07d",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 13224,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 28077,
            'title' => "Development of Musculoskeletal and Integumentary Systems-Lec",
            'description' => "Recording of this lecture (video & Ppt)",
            'uploadDate' => "2014-10-06T22:08:10+00:00",
            'originalAuthor' => "Morgan, Stephanie",
            'token' => "86ef0c2b47b4cf70c07a5323202763623b6da67f28cb40a3616709b8af0802e4",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'link' => "http://lecture.ucsf.edu/ets/Play/0e2277e8e49146e6bf431ac1e65954fc1d?catalog=d9c00909-118d-465f-9ee1-2886902b9a78",
            'type' => "link"
        );

        $arr[] = array(
            'id' => 28079,
            'title' => "Development of Musculoskeletal and Integumentary Systems-Aud",
            'description' => "MP3 audio recording of this lecture",
            'uploadDate' => "2014-10-06T22:15:38+00:00",
            'originalAuthor' => "Perez, Lisa",
            'token' => "f46627d1fd6cd52b6aa25a8a65701a2b8e07552f8746846777c371d25202f2d6",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16556/20141006-151538_616_56a39f2601095b5c3b37ac1e57e5aac1",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.mp3",
            'mimetype' => "audio/mpeg",
            'filesize' => 23465,
            'type' => "file"
        );

        $arr[] = array(
            'id' => 28414,
            'title' => "Somite Clock Video (MP4)",
            'description' => "Additional video showing the 'somite clock'",
            'uploadDate' => "2014-10-15T18:32:52+00:00",
            'originalAuthor' => "Murray, Larry",
            'token' => "ab82cf85803c12902b16591bbd8a9d55d653b09c950707bb53a8c7cc254398d8",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16556/20141015-113252_625_2b4a3343c90ff70da415fd138de5d96d",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 2318,
            'type' => "file"
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
