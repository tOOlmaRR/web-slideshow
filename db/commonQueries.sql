/****** Script for SelectTopNRows command from SSMS  ******/
USE [WebSlideshow-DEV]
GO

select * from Images
select * from Tags
select * from TaggedImages
select * from StaticSlideshows

select Images.ImageID, Images.FullFilePath, Images.FileName, Images.width, Images.height, images.Secure, Tags.Tag, Tags.Secure
from Images
join TaggedImages on Images.ImageID = TaggedImages.ImageID
join Tags on TaggedImages.TagID = Tags.TagID

--truncate table TaggedImages
--truncate table Tags
--truncate table Images

-- Process delete to a tag and all of images associated to that tag
--delete from images where imageid in (select imageid from TaggedImages where tagid = 8)
--delete from TaggedImages where tagid = 8
--delete from Tags where TagID = 8