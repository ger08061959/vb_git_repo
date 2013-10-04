This document describes the steps that are to be taken for a successful video publication.

## Videos ##

In this system, a video is just a bunch of metadata with a reference to the video in our content delivery network.

### Statuses ###

A video always has one of the following statuses:

* `Announced`:  This status indicates that metadata is filled in and &#0151; more importantly &#0151; that a video file is ready to be uploaded. Please note that the metadata can be modified at *any* stage of the process.
* `Uploaded`: This status indicates that the video has been uploaded. An uploaded video needs to be encoded first, this may take a while (from a few seconds to hours depending on the size). Once uploaded, the video can be configured to your needs.
* `Published`: This status is to be set manually. This is a useful state to check that the video is configured correctly and/or has been shared.
* `Error`: An error has occurred with this video. This usually happens when uploading an inccorrect file. In this case, a new video needs to be created.

## Workflow ##

In the [dashboard](/dashboard/), under the tab [videos](/dashboard/video/), use **[Upload Video](/dashboard/video/announce)** to make a new video. The following steps are explained below:

### Metadata ###

Fill in all metadata. Please note that the metadata can be modified at *any* stage of the process.

### Upload ###

Upload a video. Most industry standard video formats are accepted.

### Configure ###

After the video is uploaded, you can configure the video to your specifications. If some options are not available, it is because the video is in the progress of being transcoded.

#### Transcodings ####

You can select the type encoding(s) you need. The types of encoding are default values you can select for optimized video viewing. Depending on the transcoding it can take several minutes to hours te encode the video in the desired formats. Once a particular transcoding is completed, you have the option to delete it or download it.

#### Screenshots ####

You can select a screenshot that is is shown as a still in the video player.

#### Security ####

When this content protection is enabled the video is only availble within the ip ranges configured in the **IP whitelisting**, other viewers will get an error message when trying to view it. Also, using **domain whitelisting** allows certain domains to embed the video for anyone to view (in other words, this will by-pass the IP filter). For whitelisting options, please contact us.

This system prevents unauthorized viewers from viewing videos. It explicitly does not prevent people that are allowed to view a video from copying and or redistributing it.

#### Preview ####

Preview your video. Try refreshing this page if some changes have not been applied yet. Use this feature to check whether your still image and transcodings are correct. Do note that content protection is enabled meaning that it cannot be previewed if the user's IP address is not on the whitelist.

#### Share ####

Here you can copy the link to the video for distribution. Or use one of the embed codes to add the video on your website. Options include:

* Links for sharing.
* Download Screenshot.
* Links for download transcodings.
* Embed codes for websites.

### Publish ###

Use this to set your video status to `published`. This is useful for indicating that this video is configured correctly; or that it is embedded on websites.

### Statistics ###

Views en viewer statistics are available for individual videos. Use the date selectors for a specific range.